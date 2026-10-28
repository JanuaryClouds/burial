<?php

namespace Tests\Feature;

use App\Http\Controllers\Auth\UserController;
use App\Livewire\Beneficiary\Family\Index;
use App\Livewire\Client\Edit;
use App\Livewire\Notification\Index as Notifications;
use App\Models\Address;
use App\Models\Application;
use App\Models\Beneficiary;
use App\Models\Cancellation;
use App\Models\Client;
use App\Models\ClientDemographic;
use App\Models\ClientSocialInfo;
use App\Models\DocumentRequirement;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\ApplicationProcessingService;
use App\Services\Auth\AccountAccess;
use App\Services\Auth\PasswordPolicy;
use App\Services\Auth\SeederAccountContext;
use App\Services\Auth\UserService;
use App\Services\CentralClientService;
use App\Services\ClientService;
use App\Services\ImageService;
use App\Services\PsaClassificationService;
use App\Services\WorkflowVersionService;
use Database\Seeders\AdminSeeder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Livewire\Mechanisms\HandleRequests\HandleRequests;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AccountManagementTest extends TestCase
{
    public function test_client_edit_demographic_fields_bind_to_existing_form_properties(): void
    {
        $citizen = $this->account();
        $client = Client::create(['user_id' => $citizen->id, 'date_of_birth' => '1980-01-01', 'contact_number' => '09123456789']);
        ClientDemographic::create(['client_uuid' => $client->uuid, 'sex_id' => 1, 'religion_id' => 1, 'nationality_id' => 1]);
        ClientSocialInfo::create(['client_uuid' => $client->uuid, 'civil_id' => 1, 'education_id' => 1]);
        Address::create(['addressable_type' => Client::class, 'addressable_id' => $client->uuid,
            'region_code' => '13', 'region_name' => 'NCR', 'municipality_code' => '137607', 'municipality_name' => 'Taguig',
            'barangay_code' => '137607001', 'barangay_name' => 'Test', 'street' => 'Main', 'house_number' => '1']);
        $psa = \Mockery::mock(PsaClassificationService::class)->makePartial();
        foreach (['getRegionOptions', 'getProvinceOptions', 'getMunicipalityOptions', 'getBarangayOptions'] as $method) {
            $psa->shouldReceive($method)->andReturn([]);
        }
        $this->app->instance(PsaClassificationService::class, $psa);
        $this->actingAs($citizen);
        $component = Livewire::test(Edit::class, ['client' => $client->load(['address', 'demographic', 'socialInfo'])])
            ->assertSet('demographicsForm.sexId', 1)
            ->assertSet('demographicsForm.nationalityId', 1)
            ->assertSet('demographicsForm.religionId', 1);
        $html = $component->html();
        preg_match_all('/wire:model[^=]*="([^"]+)"/', $html, $bindings);
        foreach ($bindings[1] as $binding) {
            $this->assertFalse(str_ends_with($binding, '_display'), 'Selects must not invent label bindings.');
        }
        $newSex = DB::table('sexes')->insertGetId(['name' => 'Another']);
        $component->set('demographicsForm.sexId', $newSex)->call('save')->assertHasNoErrors();
        $this->assertSame($newSex, $client->demographic()->firstOrFail()->sex_id);
        $this->assertSame('NCR', $client->address()->firstOrFail()->region_name);
    }

    public function test_image_popup_renders_the_existing_view_and_keeps_access_checks(): void
    {
        $owner = $this->account();
        $client = Client::create(['user_id' => $owner->id, 'date_of_birth' => '1980-01-01', 'contact_number' => '09123456789']);
        $beneficiary = Beneficiary::create(['first_name' => 'Test', 'last_name' => 'Beneficiary', 'sex_id' => 1, 'religion_id' => 1,
            'date_of_birth' => '1960-01-01', 'date_of_death' => '2026-01-01', 'created_by' => $owner->id]);
        $application = Application::create(['client_uuid' => $client->uuid, 'beneficiary_uuid' => $beneficiary->uuid, 'relationship_id' => 1]);
        DocumentRequirement::create(['name' => 'Death Certificate', 'source' => 'Registry']);
        $this->actingAs($owner);
        $url = route('application.image.webView', [$application, 'death_certificate']);
        $this->get($url)->assertOk()->assertViewIs('application.image')->assertSee('Death Certificate');
        $this->actingAs($this->account());
        $this->getJson($url)->assertForbidden();
    }

    private const PASSWORD = 'Initial Strong 9!';

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'accounts_testing',
            'database.connections.accounts_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true],
            'cache.default' => 'array', 'session.driver' => 'array', 'hashing.bcrypt.rounds' => 4,
            'app.key' => 'base64:'.base64_encode(str_repeat('a', 32)),
            'services.portal.users.enable.get' => false, 'services.portal.users.enable.post' => false,
            'services.portal.notification.enable.post' => false, 'services.fileserver.enable.post' => false,
            'services.sms.enable.post' => false, 'services.psa_classification.get' => false,
            'services.portal.endpoint' => '', 'app.admin_password' => self::PASSWORD]);
        Http::preventStrayRequests();
        foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
            Cache::put($lookup, collect(), 3600);
        }
        Artisan::call('migrate', ['--database' => 'accounts_testing', '--force' => true]);
        foreach (['sexes', 'religions', 'relationships', 'mode_of_assistances', 'nationalities', 'civil_statuses', 'educations'] as $table) {
            DB::table($table)->insert(['name' => $table]);
        }
        SystemSetting::create(['uuid' => (string) Str::uuid(), 'maintenance_mode' => false]);
    }

    protected function tearDown(): void
    {
        DB::purge('accounts_testing');
        parent::tearDown();
    }

    private function account(?string $role = null, bool $active = true): User
    {
        $user = User::create(['citizen_uuid' => $role ? null : (string) Str::uuid(),
            'first_name' => 'Account', 'last_name' => 'Test', 'email' => Str::uuid().'@example.org',
            'contact_number' => '09123456789', 'password' => self::PASSWORD, 'is_active' => $active]);
        if ($role) {
            $user->assignRole(Role::findOrCreate('staff', 'web'));
            $user->assignRole(Role::findOrCreate($role, 'web'));
        }

        return $user;
    }

    private function signIn(User $user): void
    {
        $this->actingAs($user);
        $this->withSession(['account.staff_hash' => $user->password]);
    }

    private function profile(User $user): array
    {
        return ['firstName' => 'Changed', 'lastName' => 'Test', 'email' => $user->email,
            'contactNumber' => '09123456789', 'isActive' => true, 'roles' => []];
    }

    private function signedUrl(User $citizen): string
    {
        config(['services.portal.users.enable.get' => true, 'services.portal.sso.secret' => 'test-secret']);
        $payload = rtrim(strtr(base64_encode(json_encode(['citizen_uuid' => $citizen->citizen_uuid, 'nonce' => 'random-test-nonce'])), '+/', '-_'), '=');

        return '/sso/callback?'.http_build_query(['payload' => $payload, 'signature' => hash_hmac('sha256', $payload, 'test-secret')]);
    }

    private function snapshot(string $path, string $name): string
    {
        Livewire::withoutLazyLoading();
        $page = $this->get($path)->assertOk();
        preg_match_all('/wire:snapshot="([^"]+)"/', $page->getContent(), $matches);
        foreach ($matches[1] as $raw) {
            $snapshot = html_entity_decode($raw, ENT_QUOTES);
            if (json_decode($snapshot, true)['memo']['name'] === $name) {
                return $snapshot;
            }
        }
        $this->fail('Component snapshot not found.');
    }

    private function update(string $snapshot, array $updates, string $method = 'save')
    {
        $uri = app(HandleRequests::class)->getUpdateUri();

        return $this->postJson($uri, ['components' => [['snapshot' => $snapshot, 'updates' => $updates,
            'calls' => [['path' => '', 'method' => $method, 'params' => []]]]]], ['X-Livewire' => '']);
    }

    public function test_staff_and_citizens_use_separate_login_channels(): void
    {
        $citizen = $this->account();
        $this->post('/login/check', ['email' => $citizen->email, 'password' => self::PASSWORD]);
        $this->assertGuest();
        $this->get($this->signedUrl($citizen))->assertRedirect(route('client.create'));
        $this->assertAuthenticatedAs($citizen);
        auth()->logout();
        $staff = $this->account('staff');
        $this->post('/login/check', ['email' => $staff->email, 'password' => self::PASSWORD])->assertRedirect(route('dashboard'));
        $this->getJson($this->signedUrl($citizen))->assertForbidden();
        $this->assertAuthenticatedAs($staff);
    }

    public function test_signed_inactive_citizen_can_sign_in_without_expiration_or_upload_token(): void
    {
        $citizen = $this->account(null, false);
        $url = $this->signedUrl($citizen);
        $this->get($url)->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($citizen);
        $this->assertSame(0, $citizen->tokens()->count());
        auth()->logout();
        $this->get($url)->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($citizen);
    }

    public function test_mixed_identity_cannot_authenticate_using_signed_or_mock_callback(): void
    {
        $staff = $this->account('superadmin');
        $staff->update(['citizen_uuid' => (string) Str::uuid()]);
        $this->get($this->signedUrl($staff));
        $this->assertGuest();
        config(['services.portal.users.enable.get' => false]);
        $this->getJson('/sso/callback?citizen_uuid='.$staff->citizen_uuid)->assertForbidden();
        $this->assertGuest();
    }

    public function test_portal_email_collision_does_not_link_or_modify_staff(): void
    {
        $staff = $this->account('staff');
        config(['services.portal.users.enable.get' => true, 'services.portal.users.endpoint' => 'https://portal.test/users', 'services.portal.users.key' => 'test']);
        Http::fake(['https://portal.test/*' => Http::response(['data' => [['user_id' => (string) Str::uuid(), 'email' => $staff->email]]])]);
        $this->assertNull(app(CentralClientService::class)->checkIfUser('uuid', (string) Str::uuid()));
        $this->assertNull($staff->fresh()->citizen_uuid);
        $this->assertSame(1, User::count());
    }

    public function test_inactive_citizen_reads_own_records_but_cannot_write_or_read_others(): void
    {
        $citizen = $this->account(null, false);
        $client = Client::create(['user_id' => $citizen->id, 'date_of_birth' => '1980-01-01', 'contact_number' => '09123456789']);
        ClientDemographic::create(['client_uuid' => $client->uuid, 'sex_id' => 1, 'religion_id' => 1, 'nationality_id' => 1]);
        ClientSocialInfo::create(['client_uuid' => $client->uuid, 'civil_id' => 1, 'education_id' => 1]);
        Address::create(['addressable_type' => Client::class, 'addressable_id' => $client->uuid,
            'region_code' => '13', 'region_name' => 'NCR', 'barangay_code' => '137607001', 'barangay_name' => 'Test', 'municipality_code' => '137607', 'municipality_name' => 'Taguig', 'street' => 'Main', 'house_number' => '1']);
        $beneficiary = Beneficiary::create(['first_name' => 'Recorded', 'last_name' => 'Beneficiary', 'sex_id' => 1, 'religion_id' => 1,
            'date_of_birth' => '1960-01-01', 'date_of_death' => '2026-01-01', 'created_by' => $citizen->id]);
        Address::create(['addressable_type' => Beneficiary::class, 'addressable_id' => $beneficiary->uuid,
            'region_code' => '13', 'region_name' => 'NCR', 'barangay_code' => '137607001', 'barangay_name' => 'Test', 'street' => 'Main', 'house_number' => '1',
            'municipality_code' => '137607', 'municipality_name' => 'Taguig']);
        $application = Application::create(['client_uuid' => $client->uuid, 'beneficiary_uuid' => $beneficiary->uuid, 'relationship_id' => 1]);
        $other = Client::create(['user_id' => $this->account()->id, 'date_of_birth' => '1980-01-01', 'contact_number' => '09123456789']);
        $this->signIn($citizen);
        $this->get('/dashboard')->assertOk()->assertSee('Your Burial account is inactive.');
        $this->getJson('/client/'.$client->uuid)->assertOk();
        $this->getJson('/beneficiary/'.$beneficiary->uuid)->assertOk();
        $this->getJson('/application/'.$application->uuid)->assertOk();
        $this->getJson('/client/'.$other->uuid)->assertForbidden();
        $this->getJson('/application/create')->assertForbidden();
        $this->getJson('/client/'.$client->uuid.'/edit')->assertForbidden();
        $this->assertFalse($citizen->fresh()->can('cancel', $application));
        foreach ([
            fn () => app(ApplicationProcessingService::class)->outcome($application->uuid, $application->current_claimant_assignment_uuid, 'cancel', ['reason' => 'Not allowed']),
            fn () => app(ImageService::class)->post('not-allowed', UploadedFile::fake()->create('document.png', 1, 'image/png')),
        ] as $attempt) {
            try {
                $attempt();
                $this->fail('Inactive citizen mutation was accepted.');
            } catch (HttpException $exception) {
                $this->assertSame(403, $exception->getStatusCode());
            }
        }
        $this->assertSame(0, Cancellation::count());
        $this->expectException(HttpException::class);
        app(ClientService::class)->update($client, ['dateOfBirth' => '1970-01-01', 'contactNumber' => '09123456789']);
    }

    public function test_inactive_staff_and_superadmins_cannot_login_or_use_tokens(): void
    {
        foreach (['staff', 'superadmin'] as $role) {
            $user = $this->account($role, false);
            $this->post('/login/check', ['email' => $user->email, 'password' => self::PASSWORD]);
            $this->assertGuest();
            $token = $user->createToken('test')->plainTextToken;
            auth()->forgetGuards();
            $this->getJson('/api/user', ['Authorization' => 'Bearer '.$token])->assertForbidden();
            auth()->forgetGuards();
        }
    }

    public function test_real_livewire_request_cannot_save_after_staff_or_citizen_deactivation(): void
    {
        foreach (['staff', null] as $role) {
            $user = $this->account($role);
            $this->signIn($user);
            $snapshot = $this->snapshot('/user/'.$user->id.'/edit', 'user.edit');
            DB::table('users')->where('id', $user->id)->update(['is_active' => false]);
            $this->update($snapshot, ['form.firstName' => 'Forbidden'])->assertForbidden();
            $this->assertSame('Account', $user->fresh()->first_name);
            auth()->forgetGuards();
        }
    }

    public function test_only_owner_can_change_password_and_general_updates_reject_credentials(): void
    {
        $admin = $this->account('superadmin');
        $staff = $this->account('staff');
        $this->signIn($admin);
        foreach ([fn () => app(UserService::class)->update([...$this->profile($staff), 'newPassword' => 'Replacement 9!'], $staff),
            fn () => app(UserService::class)->changePassword($staff, ['currentPassword' => self::PASSWORD, 'newPassword' => 'Replacement 9!', 'confirmNewPassword' => 'Replacement 9!'])] as $attempt) {
            try {
                $attempt();
                $this->fail('Another account password was changed.');
            } catch (HttpException $e) {
                $this->assertSame(403, $e->getStatusCode());
            }
        }
        $this->assertTrue(Hash::check(self::PASSWORD, $staff->fresh()->password));
    }

    public function test_password_policy_validates_complexity_and_byte_limit_without_trimming_spaces(): void
    {
        foreach (['short9!', 'alllowercase9!', 'ALLUPPERCASE9!', 'MissingNumber!', 'MissingSymbol9', str_repeat('A', 70).'a9!'] as $invalid) {
            $this->assertTrue(Validator::make(['password' => $invalid], ['password' => PasswordPolicy::rules()])->fails());
        }
        $this->assertFalse(Validator::make(['password' => '  A spaced password 9!  '], ['password' => PasswordPolicy::rules()])->fails());
        $this->assertTrue(Validator::make(['password' => str_repeat('é', 35).'Aa9!'], ['password' => PasswordPolicy::rules()])->fails());
    }

    public function test_password_change_requires_current_confirmation_and_a_different_password(): void
    {
        $user = $this->account('staff');
        $this->signIn($user);
        foreach ([['currentPassword' => '', 'confirmNewPassword' => 'Replacement 9!'],
            ['currentPassword' => 'wrong', 'confirmNewPassword' => 'Replacement 9!'],
            ['currentPassword' => self::PASSWORD, 'confirmNewPassword' => 'wrong'],
            ['currentPassword' => self::PASSWORD, 'newPassword' => self::PASSWORD, 'confirmNewPassword' => self::PASSWORD]] as $override) {
            RateLimiter::clear('account.password.'.$user->id);
            try {
                app(UserService::class)->changePassword($user, [...['currentPassword' => self::PASSWORD, 'newPassword' => 'Replacement 9!', 'confirmNewPassword' => 'Replacement 9!'], ...$override]);
                $this->fail('Invalid password change accepted.');
            } catch (ValidationException) {
                $this->assertTrue(Hash::check(self::PASSWORD, $user->fresh()->password));
            }
        }
    }

    public function test_first_login_password_change_revokes_tokens_and_preserves_spaces_over_real_http(): void
    {
        $admin = $this->account('superadmin');
        $this->signIn($admin);
        $data = [...$this->profile($this->account('staff')), 'email' => Str::uuid().'@example.org', 'password' => self::PASSWORD, 'confirmPassword' => self::PASSWORD];
        $new = app(UserService::class)->store($data);
        $this->assertNull($new->citizen_uuid);
        $this->assertTrue($new->password_change_required);
        auth()->logout();
        $this->post('/login/check', ['email' => $new->email, 'password' => self::PASSWORD])->assertRedirect(route('user.password'));
        $this->get('/dashboard')->assertRedirect(route('user.password'));
        $this->assertSame(0, $new->tokens()->count());
        $snapshot = $this->snapshot('/profile/password', 'user.change-password');
        $new->createToken('old-token');
        $replacement = '  Replacement password 9!  ';
        $this->update($snapshot, ['currentPassword' => self::PASSWORD, 'newPassword' => $replacement, 'confirmNewPassword' => $replacement])->assertOk();
        $this->assertTrue(Hash::check($replacement, $new->fresh()->password));
        $this->assertFalse(Hash::check(trim($replacement), $new->fresh()->password));
        $this->assertFalse($new->fresh()->password_change_required);
        $this->assertSame(0, $new->tokens()->count());
        $this->assertGuest();
    }

    public function test_old_staff_session_is_rejected_after_password_changes(): void
    {
        $user = $this->account('staff');
        $this->signIn($user);
        $user->update(['password' => 'Replacement 9!']);
        $this->getJson('/dashboard')->assertUnauthorized();
        $this->assertGuest();
    }

    public function test_arbitrary_bearer_header_cannot_bypass_staff_session_password_validation(): void
    {
        $user = $this->account('staff');
        $this->signIn($user);
        $user->update(['password' => 'Replacement 9!']);
        $this->getJson('/dashboard', ['Authorization' => 'Bearer ignored-header'])->assertUnauthorized();
        $this->assertGuest();
    }

    public function test_existing_staff_session_without_login_baseline_must_sign_in_again(): void
    {
        $this->signIn($this->account('staff'));
        $this->withSession(['account.staff_hash' => null]);
        $this->get('/dashboard')->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_deactivation_revokes_tokens_reactivation_restores_access_and_self_disable_is_denied(): void
    {
        $admin = $this->account('superadmin');
        $other = $this->account('superadmin');
        $other->createToken('test');
        $this->signIn($admin);
        app(UserService::class)->update([...$this->profile($other), 'isActive' => false], $other);
        $this->assertFalse($other->fresh()->is_active);
        $this->assertSame(0, $other->tokens()->count());
        app(UserService::class)->update($this->profile($other), $other->fresh());
        $this->signIn($other->fresh());
        $this->get('/dashboard')->assertOk();
        $this->expectException(HttpException::class);
        app(UserService::class)->update([...$this->profile($other), 'isActive' => false], $other->fresh());
    }

    public function test_citizen_cannot_be_promoted_and_admin_seeder_rejects_citizen_collision(): void
    {
        $admin = $this->account('superadmin');
        Role::findOrCreate('CSWDO', 'web');
        $citizen = $this->account();
        $this->signIn($admin);
        try {
            app(UserService::class)->update([...$this->profile($citizen), 'roles' => ['CSWDO' => true]], $citizen);
            $this->fail('Citizen was converted to staff.');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
        $citizen->update(['email' => 'superadmin@example.org']);
        try {
            $this->seed(AdminSeeder::class);
            $this->fail('Seeder converted a citizen.');
        } catch (\RuntimeException) {
            $this->assertSame(0, $citizen->roles()->count());
        }
    }

    public function test_read_only_audit_reports_conflicts_without_changing_accounts(): void
    {
        $user = $this->account('staff');
        $user->update(['citizen_uuid' => (string) Str::uuid()]);
        $before = $user->fresh()->getAttributes();
        $this->artisan('accounts:audit')->expectsOutputToContain('Citizen UUID and internal roles coexist')->assertExitCode(1);
        $this->assertSame($before, $user->fresh()->getAttributes());
    }

    public function test_inactive_citizen_cannot_mark_notifications_read(): void
    {
        $user = $this->account(null, false);
        $this->signIn($user);
        Livewire::test(Notifications::class)->call('markAsRead', 'nonexistent')->assertForbidden();
    }

    public function test_password_changes_are_rate_limited_even_when_correct_credentials_are_later_supplied(): void
    {
        $user = $this->account('staff');
        $this->signIn($user);
        for ($attempt = 0; $attempt < 5; $attempt++) {
            try {
                app(UserService::class)->changePassword($user, ['currentPassword' => 'wrong', 'newPassword' => 'Replacement 9!', 'confirmNewPassword' => 'Replacement 9!']);
                $this->fail('Wrong password accepted.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('currentPassword', $exception->errors());
            }
        }
        try {
            app(UserService::class)->changePassword($user, ['currentPassword' => self::PASSWORD, 'newPassword' => 'Replacement 9!', 'confirmNewPassword' => 'Replacement 9!']);
            $this->fail('Rate limit was bypassed.');
        } catch (ValidationException $exception) {
            $this->assertStringContainsString('Too many attempts', $exception->errors()['currentPassword'][0]);
        }
        $this->assertTrue(Hash::check(self::PASSWORD, $user->fresh()->password));
    }

    public function test_portal_refresh_does_not_reactivate_a_locally_disabled_citizen(): void
    {
        $citizen = $this->account(null, false);
        $citizen->update(['email' => 'citizen@portal.test']);
        config(['services.portal.users.enable.get' => true, 'services.portal.users.endpoint' => 'https://portal.test/users', 'services.portal.users.key' => 'test']);
        Http::fake(['https://portal.test/*' => Http::response(['data' => [[
            'user_id' => $citizen->citizen_uuid, 'email' => $citizen->email,
            'firstname' => 'Refreshed', 'middlename' => null, 'lastname' => 'Citizen',
            'suffix' => null, 'contact_number' => '09123456789', 'is_active' => true,
        ]]])]);
        $resolved = app(CentralClientService::class)->checkIfUser('uuid', $citizen->citizen_uuid);
        $this->assertFalse($resolved->is_active);
        $this->assertSame('Refreshed', $resolved->first_name);
    }

    public function test_citizen_reactivation_restores_writes_without_restoring_tokens(): void
    {
        $admin = $this->account('superadmin');
        $citizen = $this->account(null, false);
        $this->signIn($admin);
        app(UserService::class)->update($this->profile($citizen), $citizen);
        $this->signIn($citizen->fresh());
        $client = app(ClientService::class)->store(['dateOfBirth' => '1980-01-01', 'contactNumber' => '09123456789']);
        $this->assertSame($citizen->id, $client->user_id);
        $this->assertSame(0, $citizen->tokens()->count());
    }

    public function test_inactive_citizen_cannot_use_family_member_deletion_action(): void
    {
        $citizen = $this->account(null, false);
        $beneficiary = Beneficiary::create(['first_name' => 'Recorded', 'last_name' => 'Beneficiary', 'sex_id' => 1, 'religion_id' => 1,
            'date_of_birth' => '1960-01-01', 'date_of_death' => '2026-01-01', 'created_by' => $citizen->id]);
        $member = $beneficiary->family()->create(['name' => 'Family', 'age' => 30, 'civil_id' => 1, 'sex_id' => 1, 'relationship_id' => 1]);
        $this->signIn($citizen);
        $component = new Index;
        $component->beneficiary = $beneficiary;
        try {
            $component->removeFamilyMember($member->uuid);
            $this->fail('Inactive citizen deleted a family member.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
        $this->assertTrue($member->fresh()->exists);
    }

    public function test_family_deletion_is_scoped_to_the_owned_draft(): void
    {
        $citizen = $this->account();
        $draft = fn (User $owner) => Beneficiary::create(['first_name' => 'Recorded', 'last_name' => 'Beneficiary', 'sex_id' => 1, 'religion_id' => 1,
            'date_of_birth' => '1960-01-01', 'date_of_death' => '2026-01-01', 'created_by' => $owner->id]);
        $own = $draft($citizen);
        $other = $draft($this->account());
        $family = ['name' => 'Family', 'age' => 30, 'civil_id' => 1, 'sex_id' => 1, 'relationship_id' => 1];
        $ownMember = $own->family()->create($family);
        $otherMember = $other->family()->create($family);
        $this->signIn($citizen);
        $component = new Index;
        $component->beneficiary = $own;
        $component->removeFamilyMember($ownMember->uuid);
        $this->assertNull($ownMember->fresh());
        try {
            $component->removeFamilyMember($otherMember->uuid);
            $this->fail('A family member belonging to another draft was deleted.');
        } catch (ModelNotFoundException) {
            $this->assertTrue($otherMember->fresh()->exists);
        }
    }

    public function test_request_based_account_handlers_use_the_same_creation_and_password_boundaries(): void
    {
        Route::middleware(['web', 'auth', 'active.check'])->group(function () {
            Route::post('/_test/accounts', [UserController::class, 'store']);
            Route::post('/_test/accounts/{user}', [UserController::class, 'update']);
        });
        $this->signIn($this->account('superadmin'));
        $email = Str::uuid().'@example.org';
        $this->post('/_test/accounts', ['first_name' => 'Created', 'last_name' => 'Staff', 'email' => $email,
            'contact_number' => '09123456789', 'password' => self::PASSWORD, 'password_confirmation' => self::PASSWORD])->assertRedirect();
        $staff = User::where('email', $email)->firstOrFail();
        $this->assertNull($staff->citizen_uuid);
        $this->assertTrue($staff->password_change_required);
        $this->post('/_test/accounts/'.$staff->id, ['first_name' => 'Corrected', 'last_name' => 'Staff', 'contact_number' => '09123456789'])->assertRedirect();
        $this->assertSame('Corrected', $staff->fresh()->first_name);
        $this->postJson('/_test/accounts/'.$staff->id, ['first_name' => 'Forbidden', 'last_name' => 'Staff', 'contact_number' => '09123456789',
            'password' => 'Replacement 9!', 'password_confirmation' => 'Replacement 9!'])->assertUnprocessable();
        $this->assertTrue(Hash::check(self::PASSWORD, $staff->fresh()->password));
    }

    public function test_seeding_scope_is_restored_after_exceptions_and_does_not_unlock_browser_access(): void
    {
        $admin = $this->account('superadmin');
        $admin->update(['password_change_required' => true]);
        $this->signIn($admin);
        try {
            SeederAccountContext::run(function () {
                app(AccountAccess::class)->assertWritable();
                throw new \RuntimeException('Simulated seeder failure');
            });
            $this->fail('Expected seeder failure.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Simulated seeder failure', $exception->getMessage());
        }
        $this->assertFalse(SeederAccountContext::permits($admin));
        $this->get('/dashboard')->assertRedirect(route('user.password'));
        $this->assertTrue($admin->fresh()->password_change_required);
    }

    public function test_seeding_scope_still_rejects_inactive_and_citizen_accounts(): void
    {
        foreach ([$this->account('superadmin', false), $this->account()] as $actor) {
            $this->signIn($actor);
            try {
                SeederAccountContext::run(fn () => $this->fail('Untrusted actor entered seed scope.'));
                $this->fail('Untrusted actor was accepted.');
            } catch (HttpException $exception) {
                $this->assertSame(403, $exception->getStatusCode());
            }
        }
    }

    public function test_seeding_scope_cannot_be_entered_outside_console(): void
    {
        $this->signIn($this->account('superadmin'));
        $mode = new \ReflectionProperty($this->app, 'isRunningInConsole');
        $previous = $mode->getValue($this->app);
        $mode->setValue($this->app, false);
        try {
            SeederAccountContext::run(fn () => $this->fail('Web execution entered seed scope.'));
            $this->fail('Non-console scope was accepted.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        } finally {
            $mode->setValue($this->app, $previous);
        }
    }

    public function test_seeding_scope_does_not_grant_superadmin_privileges_to_staff(): void
    {
        $staff = $this->account('staff');
        $staff->update(['password_change_required' => true]);
        $this->signIn($staff);
        try {
            SeederAccountContext::run(fn () => app(WorkflowVersionService::class)->publish([]));
            $this->fail('Staff gained workflow publication privileges.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
        $this->assertFalse(SeederAccountContext::permits($staff));
        $this->assertTrue($staff->fresh()->password_change_required);
    }
}
