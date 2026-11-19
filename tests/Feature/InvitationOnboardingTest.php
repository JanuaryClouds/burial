<?php

namespace Tests\Feature;

use App\Livewire\User\Edit;
use App\Models\MailDelivery;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\UserInvitation;
use App\Services\Auth\InvitationService;
use App\Services\Auth\SuperadminMfa;
use App\Services\Auth\UserService;
use App\Services\Mail\ImmediateMailService;
use App\Services\Mail\InvitationPayload;
use App\Services\Mail\MockEmailLogs;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Tests\TestCase;

class InvitationOnboardingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['email_logs.mock' => true, 'database.connections.email_logs_mock.database' => ':memory:']);
        app(MockEmailLogs::class)->initialize();
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
        DB::purge('email_logs_mock');
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

    private function admin(): User
    {
        $user = $this->account('superadmin');
        Role::findOrCreate('staff', 'web');
        $this->actingAs($user);
        $this->withSession(['account.staff_hash' => $user->getAuthPassword()]);

        return $user;
    }

    private function details(): array
    {
        return ['first_name' => 'New', 'last_name' => 'Owner', 'contact_number' => '09123456789',
            'password' => 'Owner password 9!', 'password_confirmation' => 'Owner password 9!'];
    }

    public function test_invitation_creates_no_user_then_assigns_latest_roles_once(): void
    {
        $this->admin();
        $role = Role::findOrCreate('CSWDO', 'web');
        $service = app(InvitationService::class);
        $invitation = $service->create(' New@Example.org ', []);
        $token = $invitation->token;
        $this->assertFalse(User::where('email', 'new@example.org')->exists());
        $this->assertNotSame($token, DB::table('user_invitations')->value('token'));
        $this->assertStringNotContainsString($token, $invitation->toJson());
        $service->manage($invitation, 'roles', [$role->name]);
        $user = $service->accept($token, $this->details());
        $this->assertTrue($user->hasAllRoles(['staff', 'CSWDO']));
        $this->assertFalse($user->password_change_required);
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue(Hash::check('Owner password 9!', $user->password));
        $this->assertNull($invitation->fresh()->token);
        $this->expectException(HttpException::class);
        $service->accept($token, $this->details());
    }

    public function test_expiry_boundary_and_logged_renewal_reuse_link_without_email(): void
    {
        $this->admin();
        $service = app(InvitationService::class);
        $invitation = $service->create('expire@example.org', []);
        $token = $invitation->token;
        $this->travelTo($invitation->expires_at);
        try {
            $service->findUsable($token);
            $this->fail('Expiry boundary must be rejected.');
        } catch (HttpException $e) {
            $this->assertSame(410, $e->getStatusCode());
        }
        $service->manage($invitation, 'renew');
        $this->assertSame($token, $invitation->fresh()->token);
        $this->assertEquals(now()->addDay()->timestamp, $invitation->fresh()->expires_at->timestamp);
        $this->assertSame(0, MailDelivery::count());
        $this->assertDatabaseHas('activity_log', ['description' => 'Invitation renewed']);
        $this->assertSame($invitation->id, $service->findUsable($token)->id);
    }

    public function test_revocation_erases_token_and_disallows_renewal_and_acceptance(): void
    {
        $this->admin();
        $service = app(InvitationService::class);
        $invitation = $service->create('revoke@example.org', []);
        $token = $invitation->token;
        $service->manage($invitation, 'revoke');
        $this->assertNull($invitation->fresh()->token);
        $this->assertNull($invitation->fresh()->pending_email);
        foreach ([fn () => $service->manage($invitation, 'renew'), fn () => $service->accept($token, $this->details())] as $attempt) {
            try {
                $attempt();
                $this->fail('Must reject revoked invitation.');
            } catch (HttpException $e) {
                $this->assertContains($e->getStatusCode(), [409, 410]);
            }
        }
        $this->assertNotSame($invitation->id, $service->create('revoke@example.org', [])->id);
    }

    public function test_duplicates_citizen_collisions_and_superadmin_roles_are_rejected(): void
    {
        $this->admin();
        $citizen = $this->account(null);
        $service = app(InvitationService::class);
        $service->create('duplicate@example.org', []);
        foreach ([fn () => $service->create('DUPLICATE@example.org', []),
            fn () => $service->create($citizen->email, []), fn () => $service->create('bad@example.org', ['superadmin'])] as $attempt) {
            try {
                $attempt();
                $this->fail('Must reject invalid invitation.');
            } catch (ValidationException) {
            }
        }
        $this->assertSame(1, UserInvitation::count());
    }

    public function test_acceptance_rechecks_existing_accounts_and_removed_roles(): void
    {
        $this->admin();
        $service = app(InvitationService::class);
        $role = Role::findOrCreate('Temporary operation', 'web');
        $invitation = $service->create('collision@example.org', [$role->name]);
        $role->delete();
        try {
            $service->accept($invitation->token, $this->details());
            $this->fail('Removed roles must fail.');
        } catch (ValidationException) {
        }
        $existing = $this->account(null);
        $existing->update(['email' => 'collision@example.org']);
        try {
            $service->accept($invitation->token, $this->details());
            $this->fail('Existing account must fail.');
        } catch (ValidationException) {
        }
        $this->assertNull($invitation->fresh()->accepted_at);
    }

    public function test_immediate_delivery_contains_branded_html_text_and_embedded_logo(): void
    {
        $this->admin();
        config(['app.name' => '<script>Unsafe</script> Burial']);
        $service = app(InvitationService::class);
        $invitation = $service->create('mail@example.org', []);
        $delivery = $service->send($invitation);
        $this->assertSame('sent', $delivery->status);
        $this->assertNull($delivery->payload);
        $message = Mail::mailer('array')->getSymfonyTransport()->messages()->first()->getOriginalMessage();
        $this->assertStringContainsString('&lt;script&gt;', $message->getHtmlBody());
        $this->assertStringNotContainsString('<script>', $message->getHtmlBody());
        $this->assertStringContainsString($invitation->token, $message->getTextBody());
        $this->assertStringContainsString('cid:', $message->getHtmlBody());
        $this->assertNotEmpty($message->getAttachments());
        $this->assertSame('site=burial', $message->getHeaders()->get('X-SES-MESSAGE-TAGS')->getBodyAsString());
        $this->assertStringNotContainsString($invitation->token, $delivery->toJson());
    }

    public function test_transport_failure_retry_renews_expired_link_and_stops_after_revocation(): void
    {
        $admin = $this->admin();
        $transport = Mail::mailer('array')->getSymfonyTransport();
        $broken = \Mockery::mock(TransportInterface::class);
        $broken->shouldReceive('send')->andThrow(new \RuntimeException('SECRET credential'));
        Mail::mailer('array')->setSymfonyTransport($broken);
        $service = app(InvitationService::class);
        $invitation = $service->create('failed@example.org', []);
        $token = $invitation->token;
        $delivery = $service->send($invitation);
        $this->assertSame('failed', $delivery->status);
        $this->assertSame('transport', $delivery->failure_code);
        $this->travel(25)->hours();
        Mail::mailer('array')->setSymfonyTransport($transport);
        $delivery = app(ImmediateMailService::class)->retry($delivery, $admin);
        $this->assertSame('sent', $delivery->status);
        $this->assertSame(2, $delivery->attempt_count);
        $this->assertTrue($invitation->fresh()->expires_at->gt(now()));
        $this->assertSame($token, $invitation->fresh()->token);
        $service->manage($invitation, 'revoke');
        $this->expectException(HttpException::class);
        app(ImmediateMailService::class)->retry($delivery, $admin);
    }

    public function test_non_admin_and_old_password_creation_are_blocked_and_profiles_are_owner_only(): void
    {
        $admin = $this->admin();
        $other = $this->account('staff');
        $users = app(UserService::class);
        try {
            $users->store([]);
            $this->fail('Temporary-password creation must be blocked.');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
        foreach ([['firstName' => 'Forged'], ['contactNumber' => '09999999999'], ['email' => 'changed@example.org'], ['password' => 'Owner password 9!']] as $changes) {
            try {
                $users->update($changes, $other);
                $this->fail('Profile tampering must be blocked.');
            } catch (HttpException $e) {
                $this->assertSame(403, $e->getStatusCode());
            }
        }
        $users->update(['isActive' => false, 'mfa_code' => SuperadminMfa::otp('JBSWY3DPEHPK3PXP')->at(now()->timestamp)], $other);
        $this->assertFalse($other->fresh()->is_active);
        $other = $other->fresh();
        $other->update(['is_active' => true]);
        $this->actingAs($other);
        $users->update(['firstName' => 'Owner changed', 'contactNumber' => '09111111111'], $other);
        $this->assertSame('Owner changed', $other->fresh()->first_name);
        $this->expectException(HttpException::class);
        app(InvitationService::class)->create('forbidden@example.org', []);
    }

    public function test_active_sending_claim_cannot_be_retried_or_bypassed_by_resend(): void
    {
        $this->admin();
        $service = app(InvitationService::class);
        $invitation = $service->create('claimed@example.org', []);
        $delivery = app(ImmediateMailService::class)->record($invitation->email, new InvitationPayload($invitation->id));
        $delivery->update(['user_invitation_id' => $invitation->id, 'status' => 'sending', 'claimed_at' => now()]);
        try {
            $service->send($invitation);
            $this->fail('Active sending must reject resend.');
        } catch (HttpException $e) {
            $this->assertSame(409, $e->getStatusCode());
        }
        $this->assertSame(1, MailDelivery::count());
    }

    public function test_public_get_does_not_consume_link_and_http_acceptance_logs_in_normally(): void
    {
        $this->admin();
        $invitation = app(InvitationService::class)->create('http@example.org', []);
        $token = $invitation->token;
        auth()->logout();
        $response = $this->get(route('invitation.form', $token))->assertOk();
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $response->assertHeader('Referrer-Policy', 'no-referrer');
        $this->assertNull($invitation->fresh()->accepted_at);
        $this->postJson(route('invitation.accept', $token), [...$this->details(), 'email' => 'attacker@example.org', 'roles' => ['superadmin']])
            ->assertOk()->assertJson(['redirect' => route('admin.login')]);
        $user = User::where('email', 'http@example.org')->firstOrFail();
        $this->assertFalse($user->hasRole('superadmin'));
        $this->post('/login/check', ['email' => $user->email, 'password' => 'Owner password 9!'])->assertRedirect(route('dashboard'));
    }

    public function test_admin_http_invites_logs_filters_and_owner_only_livewire_forms(): void
    {
        $admin = $this->admin();
        $this->postJson(route('user.invitations.store'), ['email' => 'index@example.org'])->assertOk();
        $invitation = UserInvitation::firstOrFail();
        Role::findOrCreate('CSWDO', 'web');
        $this->postJson(route('user.invitations.manage', $invitation), ['action' => 'roles', 'roles' => ['CSWDO']])->assertOk();
        $this->assertSame(['CSWDO'], $invitation->fresh()->roles);
        $this->postJson(route('user.invitations.manage', $invitation), ['action' => 'roles'])->assertOk();
        $this->assertSame([], $invitation->fresh()->roles);
        $this->get(route('user.invitations.index'))->assertOk()->assertSee('index@example.org')->assertDontSee($invitation->token);
        $this->get(route('system.failed-emails.index', ['status' => 'sent', 'type' => 'user_invitation']))->assertOk()
            ->assertSee('index@example.org')->assertSee('Accepted by SMTP')->assertDontSee($invitation->token);
        $this->get(route('system.failed-emails.index', ['status' => 'failed']))->assertOk()->assertDontSee('index@example.org');
        $other = $this->account('staff');
        Livewire::test(Edit::class, ['user' => $other])
            ->assertDontSee('First Name')->set('form.firstName', 'Tampered')->call('save');
        $this->assertSame('Recovery', $other->fresh()->first_name);
        Livewire::test(Edit::class, ['user' => $other])->set('form.isActive', false)
            ->set('mfaCode', SuperadminMfa::otp('JBSWY3DPEHPK3PXP')->at(now()->timestamp))->call('save');
        $this->assertFalse($other->fresh()->is_active);
    }

    public function test_retry_after_acceptance_cannot_resurrect_invitation_and_suppression_is_preserved(): void
    {
        $admin = $this->admin();
        $service = app(InvitationService::class);
        $invitation = $service->create('suppressed@example.org', []);
        app(MockEmailLogs::class)->append($invitation->email, 'Complaint', 'synthetic');
        $delivery = $service->send($invitation);
        $this->assertSame('suppressed', $delivery->failure_code);
        $this->assertFalse($delivery->canRetry());
        $service->accept($invitation->token, $this->details());
        $this->assertNull($delivery->fresh()->payload);
        $this->expectException(HttpException::class);
        app(ImmediateMailService::class)->retry($delivery, $admin);
    }

    public function test_async_table_reflects_acceptance_and_stale_actions_cannot_change_the_account(): void
    {
        $this->admin();
        Role::findOrCreate('CSWDO', 'web');
        $service = app(InvitationService::class);
        $invitation = $service->create('async@example.org', []);
        $token = $invitation->token;
        $snapshot = $this->getJson(route('user.invitations.index'))->assertOk()->assertJsonStructure(['html']);
        $this->assertStringContainsString('data-invitation-pending="true"', $snapshot->json('html'));
        $this->assertStringNotContainsString($token, $snapshot->getContent());
        $user = $service->accept($token, $this->details());
        $snapshot = $this->getJson(route('user.invitations.index'))->assertOk();
        $this->assertStringContainsString('Accepted', $snapshot->json('html'));
        $this->assertStringNotContainsString('<form', $snapshot->json('html'));
        foreach (['roles', 'renew', 'resend', 'revoke'] as $action) {
            $this->postJson(route('user.invitations.manage', $invitation), ['action' => $action, 'roles' => ['CSWDO']])->assertStatus(409);
        }
        $this->assertSame(['staff'], $user->fresh()->roles->pluck('name')->all());
        $this->assertSame([], $invitation->fresh()->roles);
        $this->assertStringContainsString('no-store', $snapshot->headers->get('Cache-Control'));
        auth()->logout();
        $this->post('/login/check', ['email' => $user->email, 'password' => $this->details()['password']])
            ->assertRedirect(route('dashboard'));
        $this->getJson(route('user.invitations.index'))->assertForbidden();
    }

    public function test_expired_public_page_explains_renewal_and_throttles_requests(): void
    {
        $this->admin();
        $invitation = app(InvitationService::class)->create('expired-page@example.org', []);
        auth()->logout();
        $this->travelTo($invitation->expires_at);
        $this->get(route('invitation.form', $invitation->token))->assertStatus(410)->assertSee('ask your superadmin to renew');
        $this->assertNull($invitation->fresh()->accepted_at);
        for ($attempt = 0; $attempt < 9; $attempt++) {
            $this->get(route('invitation.form', $invitation->token))->assertStatus(410);
        }
        $this->get(route('invitation.form', $invitation->token))->assertStatus(429);
    }

    public function test_invalid_password_does_not_consume_invitation_and_untrusted_mail_url_fails_safely(): void
    {
        $this->admin();
        $service = app(InvitationService::class);
        $invitation = $service->create('validation@example.org', []);
        try {
            $service->accept($invitation->token, [...$this->details(), 'password' => 'weak']);
            $this->fail('Weak password must fail.');
        } catch (ValidationException) {
        }
        $this->assertNull($invitation->fresh()->accepted_at);
        config(['app.url' => 'https://user:secret@burial.example.test']);
        $delivery = $service->send($invitation);
        $this->assertSame('configuration', $delivery->failure_code);
        $this->assertSame(0, Mail::mailer('array')->getSymfonyTransport()->messages()->count());
        $this->assertStringNotContainsString('secret', $delivery->failureDescription());
    }
}
