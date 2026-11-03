<?php

namespace Tests\Feature;

use App\Http\Controllers\Auth\PasswordRecoveryController;
use App\Livewire\ActivityLog\Index;
use App\Models\MailDelivery;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\Auth\PasswordRecoveryService;
use App\Services\Auth\UserService;
use App\Services\Mail\ImmediateMailService;
use App\Services\Mail\PasswordResetMessage;
use App\Services\Mail\PasswordResetPayload;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Tests\TestCase;

class PasswordRecoveryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'recovery_testing',
            'database.connections.recovery_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true],
            'cache.default' => 'array', 'session.driver' => 'array', 'hashing.bcrypt.rounds' => 4,
            'app.key' => 'base64:'.base64_encode(str_repeat('a', 32)), 'app.url' => 'https://burial.example.test',
            'mail.default' => 'array', 'services.portal.endpoint' => '', 'services.psa_classification.get' => false]);
        Http::preventStrayRequests();
        foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
            Cache::put($lookup, collect(), 3600);
        }
        Artisan::call('migrate', ['--database' => 'recovery_testing', '--force' => true]);
        SystemSetting::create(['uuid' => (string) Str::uuid(), 'maintenance_mode' => false]);
    }

    protected function tearDown(): void
    {
        Livewire::flushState();
        DB::purge('recovery_testing');
        parent::tearDown();
    }

    private function account(?string $role = 'staff', bool $active = true): User
    {
        $user = User::create(['first_name' => 'Recovery', 'last_name' => 'Test', 'email' => Str::uuid().'@example.org',
            'citizen_uuid' => $role ? null : Str::uuid(), 'password' => 'Original password 9!', 'is_active' => $active]);
        if ($role) {
            $user->assignRole(Role::findOrCreate($role, 'web'));
        }

        return $user;
    }

    private function record(User $user): MailDelivery
    {
        return app(ImmediateMailService::class)->record($user->email,
            new PasswordResetPayload($user->id, PasswordResetMessage::version($user), Password::createToken($user)));
    }

    private function failTransport(): void
    {
        $transport = \Mockery::mock(TransportInterface::class);
        $transport->shouldReceive('send')->andThrow(new \RuntimeException('SECRET SMTP password and token'));
        Mail::mailer('array')->setSymfonyTransport($transport);
    }

    public function test_request_sends_immediately_and_encrypts_recipient_and_payload(): void
    {
        $user = $this->account();
        $this->postJson(route('password.email'), ['email' => $user->email])->assertOk()->assertJson(['message' => PasswordRecoveryController::MESSAGE]);
        $delivery = MailDelivery::firstOrFail();
        $this->assertSame('sent', $delivery->status);
        $this->assertNull($delivery->payload);
        $this->assertSame(1, $delivery->attempt_count);
        $this->assertNotSame($user->email, DB::table('mail_deliveries')->value('recipient'));
        $messages = Mail::mailer('array')->getSymfonyTransport()->messages();
        $this->assertCount(1, $messages);
        $this->assertStringContainsString('https://burial.example.test/admin/reset-password/', $messages->first()->getOriginalMessage()->getHtmlBody());
        $this->postJson(route('password.email'), ['email' => $user->email])->assertOk();
        $this->assertSame(1, MailDelivery::count());
    }

    public function test_ineligible_and_unknown_addresses_get_the_same_response_without_mail(): void
    {
        foreach ([$this->account(null)->email, $this->account('staff', false)->email, 'unknown@example.org'] as $email) {
            $this->postJson(route('password.email'), compact('email'))->assertOk()->assertExactJson(['message' => PasswordRecoveryController::MESSAGE]);
        }
        $this->assertSame(0, MailDelivery::count());
    }

    public function test_failed_attempts_are_safe_and_manual_retry_can_succeed_only_once(): void
    {
        $user = $this->account();
        $this->failTransport();
        app(PasswordRecoveryService::class)->request($user->email);
        $delivery = MailDelivery::firstOrFail();
        $token = $delivery->payload['token'];
        $this->assertStringNotContainsString($token, DB::table('mail_deliveries')->value('payload'));
        $this->assertSame('transport', $delivery->failure_code);
        $admin = $this->account('superadmin');
        $this->actingAs($admin)->get(route('system.index'))->assertOk()->assertSee(route('system.failed-emails.index'), false);
        $this->actingAs($admin)->get(route('system.failed-emails.index'))->assertOk()->assertSee($user->email)->assertDontSee('SECRET')->assertDontSee($token);
        $this->postJson(route('system.failed-emails.retry', $delivery))->assertOk()->assertJson(['sent' => false]);
        $this->assertSame(2, $delivery->fresh()->attempt_count);
        $this->assertSame($token, $delivery->fresh()->payload['token']);
        Mail::purge('array');
        $this->postJson(route('system.failed-emails.retry', $delivery))->assertOk()->assertJson(['sent' => true]);
        $this->assertSame($admin->id, $delivery->attempts()->latest('id')->first()->actor_id);
        $this->postJson(route('system.failed-emails.retry', $delivery))->assertStatus(409);
    }

    public function test_active_claim_blocks_retry_and_stale_claim_is_preserved_as_uncertain(): void
    {
        $delivery = $this->record($this->account());
        $claim = (string) Str::uuid();
        $delivery->update(['status' => 'sending', 'claim' => $claim, 'claimed_at' => now(), 'attempt_count' => 1]);
        $delivery->attempts()->create(['claim' => $claim, 'status' => 'sending', 'started_at' => now()]);
        $this->actingAs($this->account('superadmin'));
        $this->postJson(route('system.failed-emails.retry', $delivery))->assertStatus(409);
        $delivery->update(['claimed_at' => now()->subMinutes(10)]);
        $this->get(route('system.failed-emails.index'))->assertOk()->assertSee('Uncertain');
        $this->postJson(route('system.failed-emails.retry', $delivery))->assertOk()->assertJson(['sent' => true]);
        $this->assertSame('uncertain', $delivery->attempts()->oldest('id')->first()->status);
    }

    public function test_retry_replaces_expired_or_superseded_tokens_but_never_after_password_change(): void
    {
        $user = $this->account();
        $this->failTransport();
        $delivery = app(ImmediateMailService::class)->deliver($this->record($user));
        $old = $delivery->payload['token'];
        $this->travel(61)->minutes();
        Mail::purge('array');
        $this->actingAs($this->account('superadmin'));
        $this->postJson(route('system.failed-emails.retry', $delivery))->assertOk()->assertJson(['sent' => true]);
        $this->assertFalse(Password::tokenExists($user, $old));
        $this->failTransport();
        $delivery = app(ImmediateMailService::class)->deliver($this->record($user));
        Password::createToken($user);
        Mail::purge('array');
        $this->postJson(route('system.failed-emails.retry', $delivery))->assertOk()->assertJson(['sent' => true]);
        $this->failTransport();
        $delivery = app(ImmediateMailService::class)->deliver($this->record($user));
        $user->update(['password' => 'Changed password 9!']);
        Mail::purge('array');
        $this->postJson(route('system.failed-emails.retry', $delivery))->assertOk()->assertJson(['sent' => false]);
        $this->assertSame('ineligible', $delivery->fresh()->failure_code);
        $this->assertNull($delivery->fresh()->payload);
    }

    public function test_reset_consumes_token_revokes_api_access_and_obeys_policy(): void
    {
        $user = $this->account();
        $user->update(['password_change_required' => true]);
        $user->createToken('test');
        $token = Password::createToken($user);
        $data = ['email' => $user->email, 'token' => $token, 'password' => 'weak', 'password_confirmation' => 'weak'];
        $this->postJson(route('password.update'), $data)->assertUnprocessable();
        $data['password'] = $data['password_confirmation'] = 'Original password 9!';
        $this->postJson(route('password.update'), $data)->assertUnprocessable();
        $data['password'] = $data['password_confirmation'] = '  Replacement password 9!  ';
        $this->postJson(route('password.update'), $data)->assertOk()->assertJsonStructure(['redirect']);
        $this->assertTrue(Hash::check($data['password'], $user->fresh()->password));
        $this->assertFalse($user->fresh()->password_change_required);
        $this->assertSame(0, $user->tokens()->count());
        $audit = Activity::where('description', 'Owner reset password through email recovery')->sole();
        $this->assertSame($user->id, $audit->causer_id);
        $this->assertSame($user->id, $audit->subject_id);
        $this->assertSame('email_recovery', $audit->properties->get('method'));
        $this->assertStringNotContainsString($token, $audit->properties->toJson());
        $this->assertFalse(Password::tokenExists($user, $token));
        $this->postJson(route('password.update'), $data)->assertUnprocessable();
        $this->post('/login/check', ['email' => $user->email, 'password' => $data['password']])->assertRedirect(route('dashboard'));
    }

    public function test_reset_rejects_expired_tokens_and_accounts_that_became_ineligible(): void
    {
        $user = $this->account();
        $token = Password::createToken($user);
        $data = ['email' => $user->email, 'token' => $token, 'password' => 'Replacement password 9!', 'password_confirmation' => 'Replacement password 9!'];
        $this->travel(61)->minutes();
        $this->postJson(route('password.update'), $data)->assertUnprocessable();
        $data['token'] = Password::createToken($user);
        $user->update(['is_active' => false]);
        $this->postJson(route('password.update'), $data)->assertUnprocessable();
    }

    public function test_management_requires_active_superadmin_and_recovery_pages_use_local_assets(): void
    {
        $delivery = $this->record($this->account());
        $delivery->update(['status' => 'failed']);
        $this->getJson(route('system.failed-emails.index'))->assertUnauthorized();
        foreach ([$this->account(), $this->account(null), $this->account('superadmin', false)] as $user) {
            $this->actingAs($user)->getJson(route('system.failed-emails.index'))->assertForbidden();
            $this->actingAs($user)->postJson(route('system.failed-emails.retry', $delivery))->assertForbidden();
        }
        auth()->logout();
        $this->get(route('password.reset', ['token' => 'sample', 'email' => 'a@example.org']))->assertOk()
            ->assertHeader('Referrer-Policy', 'no-referrer')->assertDontSee('cdnjs.cloudflare.com')->assertDontSee('cdn.jsdelivr.net')->assertSee('data-immediate-mail', false);
    }

    public function test_password_change_audit_and_legacy_reset_entries_identify_the_account(): void
    {
        $owner = $this->account();
        $legacy = Activity::create(['log_name' => 'account-management',
            'description' => 'Owner reset password through email recovery',
            'subject_type' => $owner->getMorphClass(), 'subject_id' => $owner->id,
            'causer_type' => null, 'causer_id' => null, 'properties' => []]);
        $this->actingAs($owner);
        request()->setLaravelSession(session()->driver());
        app(UserService::class)->changePassword($owner, ['currentPassword' => 'Original password 9!',
            'newPassword' => 'Replacement password 9!', 'confirmNewPassword' => 'Replacement password 9!']);
        $audit = Activity::where('description', 'Owner changed password')->sole();
        $this->assertSame($owner->id, $audit->causer_id);
        $this->assertSame($owner->id, $audit->subject_id);
        $this->assertSame('authenticated_password_change', $audit->properties->get('method'));
        $this->actingAs($this->account('superadmin'));
        $rows = Livewire::test(Index::class)->get('mappedLogs');
        $this->assertSame($owner->fullname().' (#'.$owner->id.')', $rows->firstWhere('id', $audit->id)['source']);
        $this->assertSame('Affected account: '.$owner->fullname().' (#'.$owner->id.'); actor not recorded', $rows->firstWhere('id', $legacy->id)['source']);
        $this->get(route('system.logs.show', $legacy))->assertOk()->assertSee('Affected Account')->assertSee('Not recorded')->assertSee($owner->fullname());
        $this->assertNull($legacy->fresh()->causer_id);
    }

    public function test_request_ip_limit_is_generic_and_reset_limit_is_separate_from_login(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson(route('password.email'), ['email' => 'unknown'.$i.'@example.org'])->assertOk();
        }
        $user = $this->account();
        $this->postJson(route('password.email'), ['email' => $user->email])->assertOk()->assertExactJson(['message' => PasswordRecoveryController::MESSAGE]);
        $this->assertSame(0, MailDelivery::count());
        for ($i = 0; $i < 5; $i++) {
            $this->postJson(route('password.update'), [])->assertUnprocessable();
        }
        $this->postJson(route('password.update'), [])->assertTooManyRequests();
        $this->post('/login/check', ['email' => $user->email, 'password' => 'Original password 9!'])->assertRedirect(route('dashboard'));
    }

    public function test_abandoned_pending_records_can_be_reviewed_and_retried(): void
    {
        $delivery = $this->record($this->account());
        $delivery->update(['created_at' => now()->subMinutes(10)]);
        $this->actingAs($this->account('superadmin'))->get(route('system.failed-emails.index'))->assertOk()->assertSee($delivery->recipient);
        $this->postJson(route('system.failed-emails.retry', $delivery))->assertOk()->assertJson(['sent' => true]);
    }

    public function test_mail_cannot_be_sent_inside_a_business_transaction(): void
    {
        $user = $this->account();
        $payload = new PasswordResetPayload($user->id, PasswordResetMessage::version($user), Password::createToken($user));
        DB::beginTransaction();
        try {
            app(ImmediateMailService::class)->send($user->email, $payload);
            $this->fail('SMTP must not run inside a business transaction.');
        } catch (\LogicException) {
            $this->assertSame(0, MailDelivery::count());
        } finally {
            DB::rollBack();
        }
    }

    public function test_production_does_not_accept_log_fallback_as_success(): void
    {
        $delivery = $this->record($this->account());
        $originalEnvironment = $this->app->environment();
        $this->app->instance('env', 'production');
        config(['mail.default' => 'failover']);
        try {
            $result = app(ImmediateMailService::class)->deliver($delivery);
            $this->assertSame('failed', $result->status);
            $this->assertSame('configuration', $result->failure_code);
        } finally {
            $this->app->instance('env', $originalEnvironment);
        }
    }

    public function test_reset_invalidates_an_existing_staff_session(): void
    {
        $user = $this->account();
        $oldHash = $user->password;
        app(PasswordRecoveryService::class)->reset(['email' => $user->email, 'token' => Password::createToken($user),
            'password' => 'Replacement password 9!', 'password_confirmation' => 'Replacement password 9!']);
        $this->actingAs($user)->withSession(['account.staff_hash' => $oldHash])->getJson('/dashboard')->assertUnauthorized();
    }

    public function test_retry_rechecks_status_email_and_roles(): void
    {
        $this->actingAs($this->account('superadmin'));
        foreach (['inactive', 'email', 'roles', 'citizen'] as $change) {
            $user = $this->account();
            $delivery = $this->record($user);
            $delivery->update(['status' => 'failed']);
            match ($change) {
                'inactive' => $user->update(['is_active' => false]),
                'email' => $user->update(['email' => 'changed@example.org']),
                'roles' => $user->syncRoles([]),
                'citizen' => $user->update(['citizen_uuid' => Str::uuid()]),
            };
            $this->postJson(route('system.failed-emails.retry', $delivery))->assertOk()->assertJson(['sent' => false]);
            $this->assertSame('ineligible', $delivery->fresh()->failure_code);
        }
    }

    public function test_mailpit_smtp_smoke_when_explicitly_enabled(): void
    {
        if (getenv('BURIAL_MAILPIT_SMOKE') !== '1') {
            $this->markTestSkipped('Set BURIAL_MAILPIT_SMOKE=1 to send synthetic local Mailpit mail.');
        }
        config(['mail.default' => 'smtp', 'mail.mailers.smtp.host' => '127.0.0.1', 'mail.mailers.smtp.port' => 1025,
            'mail.mailers.smtp.url' => null, 'mail.mailers.smtp.scheme' => 'smtp', 'mail.mailers.smtp.require_tls' => false,
            'mail.mailers.smtp.username' => null, 'mail.mailers.smtp.password' => null, 'mail.from.address' => 'burial@example.test']);
        $user = $this->account();
        $this->postJson(route('password.email'), ['email' => $user->email])->assertOk();
        $this->assertSame('sent', MailDelivery::firstOrFail()->status);
        // Force a local connection refusal, then restore the real Mailpit transport for retry.
        config(['mail.mailers.smtp.port' => 1, 'mail.mailers.smtp.timeout' => 1]);
        Mail::purge('smtp');
        $delivery = app(ImmediateMailService::class)->deliver($this->record($user));
        $this->assertSame('failed', $delivery->status);
        config(['mail.mailers.smtp.port' => 1025, 'mail.mailers.smtp.timeout' => 15]);
        Mail::purge('smtp');
        $this->actingAs($this->account('superadmin'))->postJson(route('system.failed-emails.retry', $delivery))
            ->assertOk()->assertJson(['sent' => true]);
    }
}
