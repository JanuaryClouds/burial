<?php

namespace Tests\Feature;

use App\Livewire\ActivityLog\Index;
use App\Livewire\ActivityLog\Table;
use App\Models\Notification;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\ActivityLoggerService;
use App\Services\Auth\AuthenticationAudit;
use App\Services\Auth\AuthenticationLog;
use App\Services\Auth\LoginService;
use App\Services\Auth\SuperadminMfa;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;
use Spatie\SuspiciousLogin\Fingerprinters\DeviceFingerprinter;
use Spatie\SuspiciousLogin\Models\LoginAttempt;
use Tests\TestCase;

class AuthenticationMonitoringTest extends TestCase
{
    private const PASSWORD = 'Initial Strong 9!';

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'monitor_testing',
            'database.connections.monitor_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true],
            'cache.default' => 'array', 'session.driver' => 'array', 'hashing.bcrypt.rounds' => 4,
            'app.key' => 'base64:'.base64_encode(str_repeat('a', 32)),
            'services.portal.users.enable.get' => false, 'services.portal.users.enable.post' => false,
            'services.portal.notification.enable.post' => false, 'services.fileserver.enable.post' => false,
            'services.sms.enable.post' => false, 'services.psa_classification.get' => false,
            'services.portal.endpoint' => '', 'app.admin_password' => self::PASSWORD]);
        request()->setLaravelSession(app('session.store'));
        Http::fake();
        Mail::fake();
        Queue::fake();
        Bus::fake();
        request()->headers->set('User-Agent', 'Mozilla/5.0');
        config(['services.portal.notification.enable.post' => true]);
        foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
            Cache::put($lookup, collect(), 3600);
        }
        Artisan::call('migrate', ['--database' => 'monitor_testing', '--force' => true]);
        foreach (['sexes', 'religions', 'relationships', 'mode_of_assistances', 'nationalities', 'civil_statuses', 'educations'] as $table) {
            DB::table($table)->insert(['name' => $table]);
        }
        SystemSetting::create(['uuid' => (string) Str::uuid(), 'maintenance_mode' => false]);
    }

    protected function tearDown(): void
    {
        DB::purge('monitor_testing');
        parent::tearDown();
    }

    private function staff(): User
    {
        return User::factory()->admin()->create(['password' => self::PASSWORD, 'password_change_required' => false]);
    }

    private function record(User $user, string $event = 'Password change requested'): void
    {
        app(AuthenticationAudit::class)->record($user, $event, actor: $user);
    }

    public function test_password_failures_and_success_are_recorded_once_without_external_work(): void
    {
        $user = $this->staff();
        $this->post('/login/check', ['email' => strtoupper($user->email), 'password' => 'wrong'])->assertSessionHasErrors();
        $failed = Activity::where('description', 'Password login failed')->sole();
        $this->assertNull($failed->causer_id);
        $this->assertSame($user->id, $failed->subject_id);
        $this->assertIsInt($failed->properties->get('security')['risk_score']);
        $this->assertSame(1, LoginAttempt::query()->failed()->count());
        $this->post('/login/check', ['email' => $user->email, 'password' => self::PASSWORD])->assertRedirect(route('dashboard'));
        $this->assertSame(1, LoginAttempt::query()->trusted()->count());
        $this->assertSame(1, Activity::where('description', 'Staff login completed')->count());
        Http::assertNothingSent();
        Mail::assertNothingOutgoing();
        Queue::assertNothingPushed();
        Bus::assertNothingDispatched();
    }

    public function test_only_known_staff_are_recorded_including_inactive_staff(): void
    {
        $citizen = User::factory()->create(['citizen_uuid' => Str::uuid()]);
        $inactive = $this->staff();
        $inactive->update(['is_active' => false]);
        foreach ([$citizen->email, 'unknown@example.org', $inactive->email] as $email) {
            app(LoginService::class)->attempt(['email' => $email, 'password' => 'wrong']);
        }
        $this->assertSame(1, LoginAttempt::count());
        $this->assertSame($inactive->id, LoginAttempt::sole()->authenticatable_id);
        $this->assertSame(1, Activity::where('log_name', 'authentication')->count());
    }

    public function test_cooldown_and_noncredential_events_do_not_become_failed_or_trusted_logins(): void
    {
        $user = $this->staff();
        for ($i = 0; $i < 5; $i++) {
            app(LoginService::class)->attempt(['email' => $user->email, 'password' => 'wrong']);
        }
        app(LoginService::class)->attempt(['email' => $user->email, 'password' => self::PASSWORD]);
        $this->record($user);
        $this->assertSame(5, LoginAttempt::count());
        $this->assertSame(0, LoginAttempt::query()->trusted()->count());
        $this->assertSame(1, Activity::where('description', 'Password login cooldown rejected')->count());
        $this->assertSame('scored', Activity::where('description', 'Password change requested')->sole()->properties->get('security')['assessment']);
    }

    public function test_suspicious_events_notify_owner_and_group_repeated_reasons_but_allow_escalation(): void
    {
        $user = $this->staff();
        $audit = app(AuthenticationAudit::class);
        $audit->record($user, 'First login', actor: $user, credential: true);
        request()->cookies->set('suspicious_login_device', (string) Str::uuid());
        request()->server->set('REMOTE_ADDR', '203.0.113.4');
        request()->headers->set('User-Agent', 'curl/8.0');
        $this->record($user);
        $security = Activity::where('description', 'Password change requested')->latest('id')->firstOrFail()->properties->get('security');
        $this->assertSame(55, $security['risk_score']);
        $this->assertSame('medium', $security['risk_level']);
        $this->assertCount(3, $security['reasons']);
        $this->assertSame(1, Notification::where('notifiable_id', $user->id)->count());
        $this->record($user);
        $this->assertSame(1, Notification::count());
        config(['suspicious-login.checks.new_device' => 50]);
        $this->record($user);
        $this->assertSame(2, Notification::count());
        $this->assertStringContainsString('high (80/100)', Notification::all()->map(fn ($notification) => json_decode($notification->payload, true)['body'])->implode(' '));
        $this->travel(901)->seconds();
        $this->record($user);
        $this->assertSame(3, Notification::count());
        $this->assertSame(1, LoginAttempt::count());
        Http::assertNothingSent();
        Mail::assertNothingOutgoing();
    }

    public function test_account_access_changes_notify_target_and_preserve_actor(): void
    {
        $actor = $this->staff();
        $target = $this->staff();
        config(['suspicious-login.checks.suspicious_user_agent' => 75]);
        request()->headers->set('User-Agent', 'curl');
        activity('superadmin-security')->causedBy($actor)->performedOn($actor)->withProperties(['target_id' => $target->id])->log('Internal account disabled');
        $activity = Activity::sole();
        $this->assertSame($actor->id, $activity->causer_id);
        $this->assertSame($target->id, $activity->properties->get('security')['affected_account_id']);
        $this->assertSame($target->id, Notification::sole()->notifiable_id);
    }

    public function test_failed_audit_survives_rollback_but_success_does_not(): void
    {
        $user = $this->staff();
        try {
            DB::transaction(function () use ($user) {
                app(AuthenticationAudit::class)->record($user, 'MFA verification failed', 'failure', credential: false);
                app(AuthenticationAudit::class)->record($user, 'Never committed', actor: $user);
                throw new \RuntimeException('rollback');
            });
        } catch (\RuntimeException) {
        }
        $this->assertSame(1, Activity::count());
        $this->assertSame('MFA verification failed', Activity::sole()->description);
        $this->assertSame(1, LoginAttempt::count());
    }

    public function test_mfa_pending_is_not_trusted_and_completion_is_recorded_after_commit(): void
    {
        $user = User::factory()->superadmin()->create(['password' => self::PASSWORD, 'password_change_required' => false]);
        $mfa = app(SuperadminMfa::class);
        $mfa->credential($user)->forceFill(['confirmed_at' => now(), 'secret' => 'JBSWY3DPEHPK3PXP'])->save();
        $this->post('/login/check', ['email' => $user->email, 'password' => self::PASSWORD])->assertRedirect(route('mfa.show'));
        $this->assertGuest();
        $this->assertSame(0, LoginAttempt::count());
        session(['mfa.pending.verified' => true, 'mfa.pending.ack_required' => false]);
        $mfa->finish();
        $this->assertSame(1, LoginAttempt::query()->trusted()->count());
        $this->assertSame(1, Activity::where('description', 'Superadmin completed MFA login')->count());
        $this->assertAuthenticatedAs($user);
    }

    public function test_unavailable_monitoring_does_not_prevent_login_or_claim_zero_risk(): void
    {
        $user = $this->staff();
        $this->app->bind(DeviceFingerprinter::class,
            fn () => throw new \RuntimeException('Sensitive diagnostic must not escape'));
        $this->post('/login/check', ['email' => $user->email, 'password' => self::PASSWORD])->assertRedirect(route('dashboard'));
        $security = Activity::where('description', 'Staff login completed')->sole()->properties->get('security');
        $this->assertSame('unavailable', $security['assessment']);
        $this->assertNull($security['risk_score']);
        $this->assertSame(0, Notification::count());
    }

    public function test_tables_separate_legacy_and_new_security_logs_and_keep_attribution_after_deletion(): void
    {
        $user = $this->staff();
        $this->record($user);
        $scored = Activity::sole();
        $user->syncRoles([]);
        $user->delete();
        $legacy = Activity::withoutEvents(fn () => activity('account-management')->causedByAnonymous()->log('Owner changed password'));
        $ordinary = activity('success')->causedByAnonymous()->log('Application processed');
        $admin = User::factory()->superadmin()->create();
        $this->actingAs($admin);
        $securityTable = Livewire::withoutLazyLoading()->test(Table::class, ['kind' => 'security']);
        $rows = $securityTable->get('mappedLogs')->keyBy('id');
        $this->assertCount(2, $rows);
        $this->assertIsInt($rows[$scored->id]['risk_score']);
        $this->assertSame('Not assessed', $rows[$legacy->id]['risk_score']);
        $general = Livewire::test(Table::class, ['kind' => 'general']);
        $this->assertSame([$ordinary->id], $general->get('mappedLogs')->pluck('id')->all());
        activity('success')->causedByAnonymous()->log('Another operation');
        $general->call('loadData')->assertDispatched('refresh-dataTable');
        $this->assertCount(2, $general->get('mappedLogs'));
        $this->get(route('system.logs.show', $scored))->assertOk()->assertSee('Risk Score')->assertSee('Affected Account');
        $this->get(route('system.logs.show', $legacy))->assertOk()->assertSee('Not assessed');
        Livewire::test(Index::class)->assertSee('General Activity')->assertSee('Authentication and Authorization');
    }

    public function test_staff_cannot_read_log_routes_or_invoke_log_components_but_can_read_own_alerts(): void
    {
        $user = $this->staff();
        $this->record($user);
        $this->actingAs($user);
        $this->getJson(route('system.logs.index'))->assertForbidden();
        $this->getJson(route('system.logs.show', Activity::first()))->assertForbidden();
        Livewire::test(Index::class)->assertForbidden();
        Livewire::test(Table::class, ['kind' => 'security'])->assertForbidden();
        $this->assertGreaterThan(0, Activity::where('description', 'Authorization denied')->count());
        $this->assertSame(0, LoginAttempt::count());
    }

    public function test_sensitive_input_is_not_in_audit_metadata(): void
    {
        $user = $this->staff();
        request()->merge(['password' => 'secret-password', 'token' => 'secret-token', 'mfa_code' => '123456']);
        app(AuthenticationAudit::class)->record($user, 'Test event', context: ['password' => 'secret-password', 'token' => 'secret-token', 'reason' => 'denied']);
        $properties = Activity::sole()->properties->toJson();
        foreach (['secret-password', 'secret-token', '123456'] as $secret) {
            $this->assertStringNotContainsString($secret, $properties);
        }
        $this->assertStringContainsString('denied', $properties);
    }

    public function test_pruning_is_bounded_to_old_authentication_records_and_honors_dry_run(): void
    {
        $this->travelTo(now()->startOfSecond());
        $user = $this->staff();
        app(AuthenticationAudit::class)->record($user, 'Old login', actor: $user, credential: true);
        $old = Activity::sole();
        $old->forceFill(['created_at' => now()->subDays(365)])->save();
        LoginAttempt::sole()->forceFill(['created_at' => now()->subDays(365)])->save();
        $ordinary = activity('success')->log('Keep general record');
        $ordinary->forceFill(['created_at' => now()->subDays(366)])->save();
        app(AuthenticationAudit::class)->record($user, 'Recent login', actor: $user, credential: true);
        $this->artisan('auth-monitor:prune', ['--dry-run' => true])->assertSuccessful();
        $this->assertSame(3, Activity::count());
        $this->assertSame(2, LoginAttempt::count());
        $this->artisan('auth-monitor:prune')->assertSuccessful();
        $this->assertSame(2, Activity::count());
        $this->assertSame(1, LoginAttempt::count());
        $this->assertNotNull(Activity::find($ordinary->id));
    }

    public function test_high_risk_login_is_allowed_and_scores_are_capped(): void
    {
        $user = $this->staff();
        config(['suspicious-login.checks.suspicious_user_agent' => 150]);
        $this->withHeader('User-Agent', 'curl')->post('/login/check', ['email' => $user->email, 'password' => self::PASSWORD])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $security = Activity::where('description', 'Staff login completed')->sole()->properties->get('security');
        $this->assertSame(100, $security['risk_score']);
        $this->assertSame('high', $security['risk_level']);
        $this->assertSame(1, Notification::count());
        $this->assertFalse(LoginAttempt::sole()->blocked);
        Mail::assertNothingOutgoing();
        Http::assertNothingSent();
    }

    public function test_failed_history_and_shared_ip_signals_use_only_genuine_staff_credential_attempts(): void
    {
        $owner = $this->staff();
        $audit = app(AuthenticationAudit::class);
        for ($i = 0; $i < 5; $i++) {
            $audit->record($owner, 'Password login failed', 'failure', credential: false);
        }
        $this->record($owner);
        $this->assertSame(30, Activity::where('description', 'Password change requested')->sole()->properties->get('security')['risk_score']);
        for ($i = 0; $i < 9; $i++) {
            $audit->record($this->staff(), 'Password login failed', 'failure', credential: false);
        }
        $this->record($owner, 'Review shared connection');
        $this->assertSame(70, Activity::where('description', 'Review shared connection')->sole()->properties->get('security')['risk_score']);
        $this->assertSame(14, LoginAttempt::count());
    }

    public function test_mfa_failure_records_credential_failure_without_authenticating(): void
    {
        $user = User::factory()->superadmin()->create(['password' => self::PASSWORD, 'password_change_required' => false]);
        $mfa = app(SuperadminMfa::class);
        $mfa->credential($user)->forceFill(['confirmed_at' => now(), 'secret' => 'JBSWY3DPEHPK3PXP'])->save();
        $mfa->begin($user);
        try {
            $mfa->challenge('invalid-code');
            $this->fail('Invalid MFA must be rejected.');
        } catch (ValidationException) {
        }
        $this->assertGuest();
        $this->assertSame(1, LoginAttempt::query()->failed()->count());
        $audit = Activity::where('description', 'MFA verification failed')->sole();
        $this->assertNull($audit->causer_id);
        $this->assertSame($user->id, $audit->subject_id);
    }

    public function test_missing_package_table_does_not_block_login(): void
    {
        $user = $this->staff();
        Schema::drop('login_attempts');
        $this->post('/login/check', ['email' => $user->email, 'password' => self::PASSWORD])->assertRedirect(route('dashboard'));
        $this->assertSame('unavailable', Activity::where('description', 'Staff login completed')->sole()->properties->get('security')['assessment']);
    }

    public function test_monitoring_can_be_disabled_without_changing_authentication(): void
    {
        $user = $this->staff();
        config(['suspicious-login.monitoring_enabled' => false]);
        $this->post('/login/check', ['email' => $user->email, 'password' => self::PASSWORD])->assertRedirect(route('dashboard'));
        $this->assertSame(0, LoginAttempt::count());
        $this->assertSame(0, Activity::count());
        $this->assertSame(0, Notification::count());
    }

    public function test_legacy_citizen_permission_logs_stay_out_of_staff_authentication_history(): void
    {
        $citizen = User::factory()->create(['citizen_uuid' => Str::uuid()]);
        $legacy = Activity::withoutEvents(fn () => activity('permissions')->causedBy($citizen)->log('Unauthorized access attempt'));
        $this->assertSame(0, AuthenticationLog::query(true)->count());
        $this->assertSame($legacy->id, AuthenticationLog::query(false)->sole()->id);
    }

    public function test_notifications_are_recipient_scoped_and_do_not_expose_log_links(): void
    {
        $owner = $this->staff();
        $other = $this->staff();
        config(['suspicious-login.checks.suspicious_user_agent' => 80]);
        request()->headers->set('User-Agent', 'curl');
        $this->record($owner);
        $notification = Notification::sole();
        $this->actingAs($other);
        Livewire::test(\App\Livewire\Notification\Index::class)->assertDontSee('Suspicious account activity');
        $this->actingAs($owner);
        Livewire::test(\App\Livewire\Notification\Index::class)->assertSee('Suspicious account activity')->call('markAsRead', $notification->id);
        $this->assertNotNull($notification->fresh()->read_at);
        $this->assertStringNotContainsString('/system/logs', $notification->payload);
    }

    public function test_caught_authorization_errors_are_scored_without_recording_request_secrets(): void
    {
        $user = $this->staff();
        $this->actingAs($user);
        request()->merge(['token' => 'private-token']);
        $record = ActivityLoggerService::logException(new AuthorizationException, 'Failed operation');
        $this->assertSame($user->id, $record->causer_id);
        $this->assertSame('scored', $record->properties->get('security')['assessment']);
        $this->assertStringNotContainsString('private-token', $record->properties->toJson());
        $this->assertSame(1, Activity::count());
    }

    public function test_log_component_refresh_denies_access_after_superadmin_role_revocation(): void
    {
        $admin = User::factory()->superadmin()->admin()->create();
        $this->actingAs($admin);
        $component = Livewire::test(Table::class, ['kind' => 'security']);
        $admin->removeRole('superadmin');
        $component->call('loadData')->assertForbidden();
    }
}
