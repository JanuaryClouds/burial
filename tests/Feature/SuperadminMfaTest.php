<?php

namespace Tests\Feature;

use App\Livewire\Auth\Login;
use App\Livewire\System\Settings\Edit as SettingsEdit;
use App\Livewire\User\Edit as UserEdit;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\Auth\AccountAccess;
use App\Services\Auth\LoginService;
use App\Services\Auth\PasswordRecoveryService;
use App\Services\Auth\SuperadminMfa;
use App\Services\Auth\UserService;
use App\Services\Mail\ImmediateMailService;
use App\Services\Mail\IneligibleMessage;
use App\Services\Mail\MfaSecurityMessage;
use App\Services\Mail\MfaSecurityPayload;
use App\Services\SystemSettingService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class SuperadminMfaTest extends TestCase
{
    private ?string $mysqlDatabase = null;

    private const PASSWORD = 'Mfa Test Password 9!';

    private const SECRET = 'JBSWY3DPEHPK3PXP';

    protected function setUp(): void
    {
        parent::setUp();
        $this->freezeTime();
        config(['database.default' => 'mfa_testing',
            'database.connections.mfa_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true],
            'cache.default' => 'array', 'session.driver' => 'array', 'hashing.bcrypt.rounds' => 4,
            'app.key' => 'base64:'.base64_encode(str_repeat('m', 32)), 'services.portal.endpoint' => '',
            'services.psa_classification.get' => false, 'mail.default' => 'array']);
        if (getenv('MFA_TEST_MYSQL') === '1') {
            $this->mysqlDatabase = 'burial_mfa_test_'.bin2hex(random_bytes(6));
            $connection = config('database.connections.mysql');
            config(['database.connections.mfa_server' => [...$connection, 'database' => null, 'url' => null]]);
            DB::connection('mfa_server')->statement('CREATE DATABASE `'.$this->mysqlDatabase.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            config(['database.connections.mfa_testing' => [...$connection, 'database' => $this->mysqlDatabase, 'url' => null]]);
        }
        request()->setLaravelSession(app('session.store'));
        foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
            Cache::put($lookup, collect(), 3600);
        }
        Http::preventStrayRequests();
        // Deliberate delivery failure verifies security changes survive mail outages.
        $this->mock(ImmediateMailService::class)->makePartial()->shouldReceive('send')->andThrow(new \RuntimeException('Mail unavailable'));
        Artisan::call('migrate', ['--database' => 'mfa_testing', '--force' => true]);
        SystemSetting::create(['uuid' => (string) Str::uuid(), 'maintenance_mode' => false,
            'claimant_change_limit' => 1, 'social_welfare_officer' => 'Officer', 'dept_head' => 'Head', 'department_email' => 'office@example.org']);
    }

    protected function tearDown(): void
    {
        DB::purge('mfa_testing');
        if ($this->mysqlDatabase && preg_match('/^burial_mfa_test_[a-f0-9]{12}$/D', $this->mysqlDatabase)) {
            DB::connection('mfa_server')->statement('DROP DATABASE `'.$this->mysqlDatabase.'`');
            DB::purge('mfa_server');
        }
        parent::tearDown();
    }

    private function account(string $role = 'superadmin'): User
    {
        $user = User::create(['first_name' => 'MFA', 'last_name' => 'Test', 'email' => Str::uuid().'@example.org',
            'password' => self::PASSWORD, 'is_active' => true, 'contact_number' => '09123456789']);
        $user->assignRole(Role::findOrCreate('staff', 'web'));
        $user->assignRole(Role::findOrCreate($role, 'web'));

        return $user;
    }

    private function code(string $secret = self::SECRET, int $offset = 0): string
    {
        return SuperadminMfa::otp($secret)->at(now()->timestamp + $offset);
    }

    private function invalid(callable $action, string $field = 'mfa_code'): void
    {
        try {
            $action();
            $this->fail('Expected verification rejection.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey($field, $exception->errors());
        }
    }

    private function pending(User $user): void
    {
        app(LoginService::class)->attempt(['email' => $user->email, 'password' => self::PASSWORD]);
        $this->assertGuest();
    }

    private function enroll(User $user): array
    {
        $this->pending($user);
        $mfa = app(SuperadminMfa::class);
        $secret = $mfa->enrollmentSecret();
        $mfa->enroll($this->code($secret));
        $codes = json_decode(Crypt::decryptString(session()->pull('mfa.pending.codes')), true);
        session(['mfa.pending.ack_required' => false]);
        $mfa->finish();

        return [$secret, $codes];
    }

    public function test_post_and_livewire_login_are_restricted_until_confirmed_enrollment_and_acknowledgment(): void
    {
        $user = $this->account();
        $this->post(route('login.check'), ['email' => $user->email, 'password' => self::PASSWORD])->assertRedirect(route('mfa.show'));
        $this->assertGuest();
        $this->assertNull($user->fresh()->active_session_token);
        $this->assertSame(0, $user->tokens()->count());
        $this->get(route('mfa.show'))->assertOk()->assertSee('Scan this QR code')->assertHeader('Cache-Control', 'no-store, private');
        $secret = app(SuperadminMfa::class)->enrollmentSecret();
        $this->post(route('mfa.submit'), ['action' => 'enroll', 'mfa_code' => $this->code($secret)])->assertRedirect(route('mfa.show'));
        $this->assertGuest();
        $response = $this->get(route('mfa.show'))->assertOk()->assertSee('Save your recovery codes');
        $codes = $response->viewData('codes');
        $this->assertCount(10, $codes);
        $this->assertCount(10, array_unique($codes));
        $this->assertSame(32, strlen($codes[0]));
        $this->get(route('mfa.show'))->assertViewHas('codes', []);
        $this->post(route('mfa.submit'), ['action' => 'ack'])->assertSessionHasErrors('saved_codes');
        $this->post(route('mfa.submit'), ['action' => 'ack', 'saved_codes' => '1'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->active_session_token);
        $this->assertSame(1, $user->tokens()->count());
        $this->assertSame(app(SuperadminMfa::class)->credential($user)->version, $user->tokens()->first()->mfa_version);
        auth()->logout();
        session()->flush();
        Livewire::test(Login::class)->set('email', $user->email)->set('password', self::PASSWORD)->call('submit')->assertRedirect(route('mfa.show'));
        $this->assertGuest();
    }

    public function test_every_login_requires_a_new_code_and_only_completed_login_replaces_the_active_session(): void
    {
        $user = $this->account();
        [$secret] = $this->enroll($user);
        $active = $user->fresh()->active_session_token;
        $this->pending($user);
        $this->assertSame($active, $user->fresh()->active_session_token);
        $this->invalid(fn () => app(SuperadminMfa::class)->challenge($this->code($secret)));
        $this->travel(30)->seconds();
        app(SuperadminMfa::class)->challenge($this->code($secret));
        app(SuperadminMfa::class)->finish();
        $this->assertNotSame($active, $user->fresh()->active_session_token);
        $this->assertAuthenticatedAs($user);
    }

    public function test_pending_login_expires_and_password_or_credential_changes_invalidate_it(): void
    {
        $user = $this->account();
        $this->pending($user);
        $this->travel(601)->seconds();
        $this->invalid(fn () => app(SuperadminMfa::class)->pendingUser());
        $this->pending($user);
        $user->update(['password' => 'Another Strong Password 9!']);
        $this->invalid(fn () => app(SuperadminMfa::class)->pendingUser());
    }

    public function test_initial_password_change_remains_restricted_before_mfa(): void
    {
        $user = $this->account();
        $user->update(['password_change_required' => true]);
        $this->pending($user);
        $this->get(route('mfa.show'))->assertViewHas('stage', 'password');
        $this->post(route('mfa.submit'), ['action' => 'password', 'password' => 'Changed Password 9!', 'password_confirmation' => 'Changed Password 9!'])->assertRedirect(route('mfa.show'));
        $this->assertGuest();
        $this->assertFalse($user->fresh()->password_change_required);
        $this->assertSame(0, $user->tokens()->count());
        $this->get(route('mfa.show'))->assertViewHas('stage', 'enroll');
    }

    public function test_standard_totp_vector_and_clock_window_with_replay_prevention(): void
    {
        // RFC 6238 SHA-1 vector at Unix time 59, truncated to six digits.
        $this->assertSame('287082', SuperadminMfa::otp('GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ')->at(59));
        $user = $this->account();
        $this->actingAs($user);
        $mfa = app(SuperadminMfa::class);
        $mfa->protect($user, $this->code(offset: -30), fn () => null);
        $mfa->protect($user, $this->code(), fn () => null);
        $mfa->protect($user, $this->code(offset: 30), fn () => null);
        $this->invalid(fn () => $mfa->protect($user, $this->code(), fn () => null));
        $this->invalid(fn () => $mfa->protect($user, $this->code(offset: 90), fn () => null));
    }

    public function test_failed_mfa_attempts_share_one_account_throttle_across_endpoints(): void
    {
        $user = $this->account();
        $this->actingAs($user);
        $this->pending($user);
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('mfa.submit'), ['action' => $i % 2 ? 'recover' : 'challenge', 'mfa_code' => 'bad'])->assertSessionHasErrors('mfa_code');
        }
        $this->post(route('mfa.submit'), ['action' => 'challenge', 'mfa_code' => $this->code()])->assertSessionHasErrors('mfa_code');
        $this->assertGuest();
        $this->travel(61)->seconds();
        $this->post(route('mfa.submit'), ['action' => 'challenge', 'mfa_code' => $this->code()])->assertRedirect(route('dashboard'));
    }

    public function test_recovery_code_invalidates_old_credentials_and_only_allows_replacement(): void
    {
        $user = $this->account();
        [$secret, $codes] = $this->enroll($user);
        $oldVersion = app(SuperadminMfa::class)->credential($user)->version;
        $this->pending($user);
        app(SuperadminMfa::class)->recover($codes[0]);
        $credential = app(SuperadminMfa::class)->credential($user);
        $this->assertNull($credential->secret);
        $this->assertNull($credential->confirmed_at);
        $this->assertSame([], $credential->recovery_hashes);
        $this->assertNotSame($oldVersion, $credential->version);
        $this->assertSame(0, $user->tokens()->count());
        $this->assertGuest();
        $this->get(route('mfa.show'))->assertViewHas('stage', 'enroll');
        $this->invalid(fn () => app(SuperadminMfa::class)->recover($codes[0]));
        $this->invalid(fn () => app(SuperadminMfa::class)->recover($codes[1]));
        // Interruption does not restore the old factor or recovery set.
        $this->pending($user);
        $this->get(route('mfa.show'))->assertViewHas('stage', 'enroll');
    }

    public function test_regeneration_and_replacement_require_password_plus_a_fresh_code(): void
    {
        $user = $this->account();
        [$secret, $oldCodes] = $this->enroll($user);
        $this->travel(30)->seconds();
        $this->invalid(fn () => app(SuperadminMfa::class)->manage($user, 'wrong', $this->code($secret), false), 'password');
        app(SuperadminMfa::class)->manage($user, self::PASSWORD, $this->code($secret), false);
        $this->assertGuest();
        $this->assertTrue(session('mfa.pending.ack_required'));
        $this->assertNotContains(hash('sha256', $oldCodes[0]), app(SuperadminMfa::class)->credential($user)->recovery_hashes);
        session()->pull('mfa.pending.codes');
        session(['mfa.pending.ack_required' => false]);
        app(SuperadminMfa::class)->finish();
        $this->travel(30)->seconds();
        app(SuperadminMfa::class)->manage($user, self::PASSWORD, $this->code($secret), true);
        $this->assertGuest();
        $this->assertNull(app(SuperadminMfa::class)->credential($user)->confirmed_at);
    }

    public function test_settings_service_and_livewire_require_a_fresh_code_per_save(): void
    {
        $user = $this->account();
        $this->actingAs($user);
        $service = app(SystemSettingService::class);
        $this->invalid(fn () => $service->update(['claimant_change_limit' => 5]));
        $this->assertSame(1, SystemSetting::first()->claimant_change_limit);
        $service->update(['claimant_change_limit' => 5, 'mfa_code' => $this->code()]);
        $this->invalid(fn () => $service->update(['claimant_change_limit' => 6, 'mfa_code' => $this->code()]));
        $this->assertSame(5, SystemSetting::first()->claimant_change_limit);
        $this->travel(30)->seconds();
        Livewire::test(SettingsEdit::class)->set('form.claimantChangeLimit', 7)->set('mfaCode', $this->code())->call('save')->assertHasNoErrors()->assertSet('mfaCode', '');
        $this->assertSame(7, SystemSetting::first()->claimant_change_limit);
        Livewire::test(SettingsEdit::class)->set('form.claimantChangeLimit', 8)->call('save')->assertHasErrors('mfa_code')->assertSet('mfaCode', '');
        $this->assertSame(7, SystemSetting::first()->claimant_change_limit);
    }

    public function test_rejected_settings_input_does_not_consume_code_and_failed_mfa_is_audited_after_rollback(): void
    {
        $user = $this->account();
        $this->actingAs($user);
        $this->invalid(fn () => app(SystemSettingService::class)->update(['department_email' => 'invalid', 'mfa_code' => $this->code()]), 'department_email');
        $this->assertNull(app(SuperadminMfa::class)->credential($user)->last_used_step);
        $this->invalid(fn () => app(SystemSettingService::class)->update(['claimant_change_limit' => 4, 'mfa_code' => 'bad']));
        $this->assertDatabaseHas('activity_log', ['description' => 'MFA verification failed']);
        app(SystemSettingService::class)->update(['claimant_change_limit' => 4, 'mfa_code' => $this->code()]);
        $this->assertSame(4, SystemSetting::first()->claimant_change_limit);
    }

    public function test_livewire_login_and_mfa_setup_remain_available_during_maintenance(): void
    {
        $user = $this->account();
        SystemSetting::first()->update(['maintenance_mode' => true]);
        $component = Livewire::test(Login::class);
        $this->get(route('admin.login'))->assertOk();
        $this->postJson(app('livewire')->getUpdateUri(), ['components' => [[
            'snapshot' => json_encode($component->snapshot),
            'updates' => ['email' => $user->email, 'password' => self::PASSWORD],
            'calls' => [['method' => 'submit', 'params' => []]],
        ]]], ['X-Livewire' => 'true'])->assertOk();
        $this->assertGuest();
        $this->get(route('mfa.show'))->assertOk()->assertViewHas('stage', 'enroll');
    }

    public function test_internal_disabling_needs_mfa_and_citizen_disabling_does_not(): void
    {
        $actor = $this->account();
        $target = $this->account('staff');
        $this->actingAs($actor);
        $this->invalid(fn () => app(UserService::class)->update(['isActive' => false], $target));
        $this->assertTrue($target->fresh()->is_active);
        Livewire::test(UserEdit::class, ['user' => $target])->set('form.isActive', false)->set('mfaCode', $this->code())->call('save')->assertHasNoErrors()->assertSet('mfaCode', '');
        $this->assertFalse($target->fresh()->is_active);
        $citizen = User::create(['first_name' => 'Citizen', 'last_name' => 'Test', 'email' => Str::uuid().'@example.org',
            'citizen_uuid' => (string) Str::uuid(), 'password' => self::PASSWORD, 'is_active' => true]);
        app(UserService::class)->update(['isActive' => false], $citizen);
        $this->assertFalse($citizen->fresh()->is_active);
    }

    public function test_role_changes_are_explicit_protected_and_revoke_the_targets_access(): void
    {
        $actor = $this->account();
        $target = $this->account('staff');
        $target->createToken('old');
        $this->actingAs($actor);
        $this->invalid(fn () => app(UserService::class)->setSuperadmin($target, true, ''));
        Livewire::test(UserEdit::class, ['user' => $target])->set('mfaCode', $this->code())->call('changeSuperadmin', true)->assertHasNoErrors()->assertSet('mfaCode', '')
            ->assertDispatched('notification:alert', ['type' => 'success', 'title' => 'Superadmin access granted', 'text' => 'This account now has superadmin access.']);
        $this->assertTrue($target->fresh()->hasRole('superadmin'));
        $this->assertSame(0, $target->tokens()->count());
        $this->assertNull(app(SuperadminMfa::class)->credential($target)->confirmed_at);
        $this->travel(30)->seconds();
        Livewire::test(UserEdit::class, ['user' => $target])->set('mfaCode', $this->code())->call('changeSuperadmin', false)->assertHasNoErrors()->assertSet('mfaCode', '')
            ->assertDispatched('notification:alert', ['type' => 'success', 'title' => 'Superadmin access removed', 'text' => 'This account no longer has superadmin access.']);
        $this->assertFalse($target->fresh()->hasRole('superadmin'));
        $this->assertTrue($target->fresh()->hasRole('staff'));
        $this->expectException(HttpException::class);
        app(UserService::class)->setSuperadmin($actor, false, $this->code());
    }

    public function test_legacy_sessions_livewire_calls_and_pre_mfa_tokens_cannot_bypass_verification(): void
    {
        $user = $this->account();
        $this->actingAs($user);
        session()->forget('mfa.proof');
        Livewire::test(SettingsEdit::class)->assertForbidden();
        try {
            app(AccountAccess::class)->assertWritable();
            $this->fail('Direct service access was allowed.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
        $this->get(route('dashboard'))->assertRedirect(route('admin.login'));
        $this->assertGuest();
        $token = $user->createToken('legacy');
        $this->withHeader('Authorization', 'Bearer '.$token->plainTextToken)->getJson('/api/user')->assertForbidden();
    }

    public function test_password_recovery_preserves_mfa_and_requires_it_at_next_login(): void
    {
        $user = $this->account();
        $this->actingAs($user);
        $credential = app(SuperadminMfa::class)->credential($user);
        $token = Password::broker()->createToken($user);
        app(PasswordRecoveryService::class)->reset(['email' => $user->email, 'token' => $token,
            'password' => 'Recovered Password 9!', 'password_confirmation' => 'Recovered Password 9!']);
        $this->assertSame($credential->secret, $credential->fresh()->secret);
        $this->assertSame($credential->version, $credential->fresh()->version);
        app(LoginService::class)->attempt(['email' => $user->email, 'password' => 'Recovered Password 9!']);
        $this->assertGuest();
        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_encrypted_secrets_and_hashed_recovery_codes_never_serialize_or_enter_audit_logs(): void
    {
        $user = $this->account();
        [$secret, $codes] = $this->enroll($user);
        $credential = app(SuperadminMfa::class)->credential($user);
        $raw = DB::table('superadmin_mfa_credentials')->where('user_id', $user->id)->first();
        $this->assertNotSame($secret, $raw->secret);
        foreach ([$credential->toJson(), $user->toJson(), json_encode(DB::table('activity_log')->get()), $raw->recovery_hashes] as $serialized) {
            $this->assertStringNotContainsString($secret, $serialized);
            foreach ($codes as $code) {
                $this->assertStringNotContainsString($code, $serialized);
            }
        }
    }

    public function test_console_reset_is_interactive_audited_and_invalidates_pending_logins(): void
    {
        $user = $this->account();
        $this->enroll($user);
        $this->pending($user);
        $this->artisan('superadmin:mfa-reset', ['email' => $user->email])
            ->expectsQuestion('Audit reason and operator identity (no passwords, secrets, or codes)', 'Verified in person by test operator')
            ->expectsConfirmation('Have you verified ownership outside the application and authorize revoking all MFA credentials, sessions, and API tokens?', 'yes')
            ->assertSuccessful();
        $this->assertNull(app(SuperadminMfa::class)->credential($user)->secret);
        $this->assertSame(0, $user->tokens()->count());
        $this->invalid(fn () => app(SuperadminMfa::class)->pendingUser());
        $this->assertDatabaseHas('activity_log', ['description' => 'Operator reset superadmin MFA']);
    }

    public function test_security_mail_is_tracked_filterable_and_contains_no_credentials(): void
    {
        $user = $this->account();
        $this->actingAs($user);
        $delivery = (new ImmediateMailService)->record($user->email,
            new MfaSecurityPayload($user->id, 'Authenticator enrolled'));
        $message = (new MfaSecurityMessage)->prepare($delivery);
        $this->assertStringContainsString('Authenticator enrolled', $message->render());
        $this->assertStringContainsString('City Social Welfare and Development Office', $message->render());
        $this->assertStringContainsString('Never share authenticator or recovery codes.', $message->render());
        $this->assertStringNotContainsString(self::SECRET, $message->render());
        $this->get(route('system.failed-emails.index', ['type' => 'mfa_security']))->assertOk()->assertSee('Account security');
        $user->update(['email' => 'changed-owner@example.org']);
        $this->expectException(IneligibleMessage::class);
        (new MfaSecurityMessage)->prepare($delivery);
    }

    public function test_mysql_concurrent_code_recovery_and_last_administrator_changes(): void
    {
        if (! $this->mysqlDatabase) {
            $this->markTestSkipped('Set MFA_TEST_MYSQL=1 to verify concurrency in disposable MySQL databases.');
        }
        $first = $this->account();
        $second = $this->account();
        foreach (['totp', 'recovery', 'disable', 'demote'] as $operation) {
            foreach ([$first, $second] as $user) {
                $user->update(['is_active' => true]);
                $user->assignRole('superadmin');
                app(SuperadminMfa::class)->credential($user)->forceFill(['secret' => self::SECRET,
                    'confirmed_at' => now(), 'last_used_step' => null, 'recovery_hashes' => [hash('sha256', str_repeat('a', 32))]])->save();
            }
            $directory = sys_get_temp_dir().'/burial-mfa-concurrency-'.bin2hex(random_bytes(8));
            mkdir($directory);
            $workers = [];
            try {
                for ($i = 0; $i < 2; $i++) {
                    $actor = in_array($operation, ['disable', 'demote'], true) && $i === 1 ? $second : $first;
                    $target = $actor->id === $first->id ? $second : $first;
                    $worker = new Process([PHP_BINARY, base_path('tests/Support/mfa-concurrency.php'),
                        $operation, (string) $actor->id, (string) $target->id, $directory, (string) $i], base_path(),
                        ['MFA_TEST_CONNECTION' => json_encode(config('database.connections.mfa_testing')),
                            'MFA_TEST_KEY' => config('app.key'), 'MFA_TEST_TIMESTAMP' => (string) now()->timestamp]);
                    $worker->setTimeout(30);
                    $worker->start();
                    $workers[] = $worker;
                }
                $deadline = microtime(true) + 20;
                while (! is_file($directory.'/ready-0') || ! is_file($directory.'/ready-1')) {
                    foreach ($workers as $worker) {
                        if (! $worker->isRunning()) {
                            $this->fail($worker->getOutput().$worker->getErrorOutput());
                        }
                    }
                    if (microtime(true) >= $deadline) {
                        $this->fail('Workers did not reach the barrier.');
                    }
                    usleep(10000);
                }
                file_put_contents($directory.'/go', 'go');
                $results = [];
                foreach ($workers as $worker) {
                    $worker->wait();
                    $this->assertTrue($worker->isSuccessful(), $worker->getOutput().$worker->getErrorOutput());
                    $results[] = trim($worker->getOutput());
                }
                sort($results);
                $this->assertSame(['accepted', 'rejected'], $results, $operation);
                $this->assertGreaterThanOrEqual(1, User::role('superadmin')->where('is_active', true)->count());
            } finally {
                foreach ($workers as $worker) {
                    if ($worker->isRunning()) {
                        $worker->stop();
                    }
                }
                foreach (glob($directory.'/*') as $file) {
                    unlink($file);
                }
                rmdir($directory);
            }
        }
    }
}
