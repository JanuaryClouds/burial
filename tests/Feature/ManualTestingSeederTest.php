<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workflow;
use App\Services\Auth\AccountAccess;
use App\Services\Auth\SeederAccountContext;
use Database\Seeders\AdminSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\WorkflowSeeder;
use Database\Seeders\WorkflowStageSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class ManualTestingSeederTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'manual_seed_testing',
            'database.connections.manual_seed_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true],
            'cache.default' => 'array', 'app.key' => 'base64:'.base64_encode(str_repeat('s', 32)),
            'app.admin_password' => 'Manual Test Password 9!', 'services.portal.users.seeder.from_portal' => false]);
        foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
            Cache::put($lookup, collect(), 3600);
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Artisan::call('migrate', ['--database' => 'manual_seed_testing', '--force' => true]);
        Http::preventStrayRequests();
        $this->seed([PermissionSeeder::class, RoleSeeder::class]);
    }

    protected function tearDown(): void
    {
        DB::purge('manual_seed_testing');
        parent::tearDown();
    }

    public function test_seeded_internal_accounts_cover_office_and_department_boundaries(): void
    {
        $this->seed([UserSeeder::class, AdminSeeder::class]);
        $this->assertSame(41, User::count());
        $staff = User::where('email', 'staff1@example.org')->firstOrFail();
        $this->assertSame(['staff'], $staff->roles->pluck('name')->all());
        $reader = User::where('email', 'staff2@example.org')->firstOrFail();
        $this->assertTrue($reader->hasOfficeAccess());
        $this->assertFalse($reader->canOfficeAction('Create Assessment'));
        $assessor = User::where('email', 'cswdo-assessment@example.org')->firstOrFail();
        $this->assertTrue($assessor->canOfficeAction('Create Assessment'));
        $this->assertFalse($assessor->canOfficeAction('Create Interview'));
        $this->assertFalse($assessor->canOfficeAction('Create Recommendation'));
        $full = User::where('email', 'cswdo-full@example.org')->firstOrFail();
        foreach (['Create Interview', 'Create Assessment', 'Create Recommendation', 'Create Referral', 'Create Rejection', 'Create Report', 'Create Closure'] as $permission) {
            $this->assertTrue($full->canOfficeAction($permission), $permission);
        }
        foreach (['admin' => 'Admin', 'maricar' => 'Maricar', 'emma' => 'Emma', 'nikki' => 'Nikki', 'bao' => 'Bao', 'budget' => 'Budget', 'accounting' => 'Accounting', 'treasury' => 'Treasury', 'releasing' => 'Releasing'] as $login => $stage) {
            $user = User::where('email', 'workflow-'.$login.'@example.org')->firstOrFail();
            $this->assertTrue($user->hasRole('staff'));
            $this->assertFalse($user->hasOfficeAccess());
            $this->assertSame(['Workflow '.$stage], $user->effectivePermissionNames());
            $this->assertTrue($user->canSearchApplications());
        }
        $this->assertTrue(Hash::check('Manual Test Password 9!', $staff->password));
        $this->assertSame(0, Role::findByName('staff', 'web')->permissions()->count());
        $this->assertSame(0, Role::findByName('CSWDO', 'web')->permissions()->count());
    }

    public function test_rerunning_seeders_preserves_uat_identities_and_passwords_and_does_not_randomize_staff(): void
    {
        $this->seed([UserSeeder::class, AdminSeeder::class]);
        $client = User::where('email', 'client1@tlcportal.com')->firstOrFail();
        $client->update(['password' => 'portal-uat-password']);
        $snapshot = User::where('email', 'like', '%@tlcportal.com')->orderBy('email')->get()->mapWithKeys(fn ($user) => [$user->email => $user->getAttributes()])->all();
        $staff = User::where('email', 'staff1@example.org')->firstOrFail();
        $staffPassword = $staff->password;
        $adminPassword = User::where('email', 'superadmin@example.org')->firstOrFail()->password;
        $staff->assignRole('CSWDO');
        $staff->givePermissionTo('Create Assessment');
        config(['app.admin_password' => 'changed-default-password']);
        $this->seed([PermissionSeeder::class, RoleSeeder::class, UserSeeder::class, AdminSeeder::class]);
        $this->assertSame(41, User::count());
        $this->assertSame($snapshot, User::where('email', 'like', '%@tlcportal.com')->orderBy('email')->get()->mapWithKeys(fn ($user) => [$user->email => $user->getAttributes()])->all());
        $this->assertSame('fcf7c561-138b-4880-abae-e1ead49cfb94', $client->fresh()->citizen_uuid);
        $this->assertTrue(Hash::check('portal-uat-password', $client->fresh()->password));
        $this->assertSame(20, User::where('email', 'like', '%@tlcportal.com')->whereDoesntHave('roles')->count());
        $this->assertSame(['staff'], $staff->fresh()->roles->pluck('name')->all());
        $this->assertSame([], $staff->fresh()->effectivePermissionNames());
        $this->assertSame($staffPassword, $staff->fresh()->password);
        $this->assertSame($adminPassword, User::where('email', 'superadmin@example.org')->firstOrFail()->password);
    }

    public function test_portal_mode_preserves_existing_client_credentials_and_citizen_links(): void
    {
        config(['services.portal.users.seeder.from_portal' => true, 'services.portal.users.enable.get' => true,
            'services.portal.users.endpoint' => 'https://portal.example.test/users', 'services.portal.users.key' => 'test-only-key']);
        $responses = Http::fakeSequence();
        for ($run = 0; $run < 2; $run++) {
            for ($number = 1; $number <= 10; $number++) {
                $responses->push(['data' => [['email' => 'client'.$number.'@tlcportal.com',
                    'user_id' => sprintf('00000000-0000-4000-8000-%012d', $number), 'firstname' => 'Portal',
                    'lastname' => 'Client '.$number, 'contact_number' => '09123456789']]]);
            }
        }
        $existing = User::create(['email' => 'client1@tlcportal.com', 'citizen_uuid' => 'fcf7c561-138b-4880-abae-e1ead49cfb94',
            'first_name' => 'Existing', 'last_name' => 'UAT Client', 'password' => 'existing-uat-password']);
        $before = $existing->fresh()->getAttributes();
        $this->seed(UserSeeder::class);
        $this->seed(UserSeeder::class);
        $this->assertSame($before, $existing->fresh()->getAttributes());
        $this->assertSame(30, User::count());
        $this->assertSame(10, User::where('email', 'like', '%@tlcportal.com')->whereDoesntHave('roles')->count());
        Http::assertSentCount(20);
    }

    public function test_internal_fixture_roles_never_replace_a_portal_citizen_account(): void
    {
        $citizen = User::create(['email' => 'staff1@example.org', 'citizen_uuid' => 'fcf7c561-138b-4880-abae-e1ead49cfb94',
            'first_name' => 'Portal', 'last_name' => 'Citizen', 'password' => 'uat-password']);
        $before = $citizen->fresh()->getAttributes();
        try {
            $this->seed(UserSeeder::class);
            $this->fail('A portal-linked user was converted into internal staff.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('portal citizen', $exception->getMessage());
        }
        $this->assertSame($before, $citizen->fresh()->getAttributes());
        $this->assertFalse($citizen->fresh()->isInternal());
    }

    public function test_invalid_initial_password_is_named_without_exposing_it_or_creating_accounts(): void
    {
        $secret = 'weak-test-secret';
        config(['app.admin_password' => $secret]);
        foreach ([UserSeeder::class, AdminSeeder::class] as $seeder) {
            try {
                $this->seed($seeder);
                $this->fail('Invalid initial password was accepted.');
            } catch (\RuntimeException $exception) {
                $this->assertStringContainsString('ADMIN_PASSWORD', $exception->getMessage());
                $this->assertStringContainsString('uppercase', $exception->getMessage());
                $this->assertStringNotContainsString($secret, $exception->getMessage());
            }
            $this->assertSame(0, User::count());
        }
        Http::assertNothingSent();
    }

    public function test_workflow_seeders_support_initial_password_accounts_without_clearing_the_flag(): void
    {
        $this->seed([UserSeeder::class, AdminSeeder::class]);
        $admin = User::role('superadmin')->firstOrFail();
        $hash = $admin->password;
        $citizen = User::where('email', 'client1@tlcportal.com')->firstOrFail();
        $this->actingAs($citizen);
        $this->seed([WorkflowSeeder::class, WorkflowStageSeeder::class]);
        $this->assertSame(1, Workflow::count());
        $this->assertNotNull(Workflow::firstOrFail()->published_at);
        $this->assertSame($citizen->id, auth()->id());
        $this->assertTrue($admin->fresh()->password_change_required);
        $this->assertSame($hash, $admin->fresh()->password);
        $this->assertFalse(SeederAccountContext::permits($admin));
        $this->actingAs($admin->fresh());
        $this->expectException(HttpException::class);
        app(AccountAccess::class)->assertWritable();
    }
}
