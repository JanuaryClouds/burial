<?php

namespace Tests\Feature;

use App\Livewire\Dashboard;
use App\Models\Application;
use App\Models\Beneficiary;
use App\Models\Client;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'dashboard_testing',
            'database.connections.dashboard_testing' => [
                'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true,
            ],
            'cache.default' => 'array',
            'session.driver' => 'array',
            'app.key' => 'base64:'.base64_encode(str_repeat('d', 32)),
        ]);
        foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
            Cache::put($lookup, collect(), 3600);
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Artisan::call('migrate', ['--database' => 'dashboard_testing', '--force' => true]);
        foreach (['sexes', 'religions', 'relationships'] as $table) {
            DB::table($table)->insert(['name' => $table]);
        }
    }

    protected function tearDown(): void
    {
        DB::purge('dashboard_testing');
        parent::tearDown();
    }

    private function user(?string $role = null): User
    {
        $user = User::create(['first_name' => 'Dashboard', 'last_name' => 'User',
            'email' => Str::uuid().'@example.test', 'password' => 'test-password', 'is_active' => true]);
        if ($role) {
            $user->assignRole(Role::findOrCreate($role, 'web'));
        }

        return $user;
    }

    private function application(?User $user = null): Application
    {
        $user ??= $this->user();
        $client = Client::create(['user_id' => $user->id, 'date_of_birth' => '1980-01-01', 'contact_number' => '09123456789']);
        $beneficiary = Beneficiary::create(['first_name' => 'Test', 'last_name' => 'Beneficiary',
            'sex_id' => 1, 'religion_id' => 1, 'date_of_birth' => '1960-01-01',
            'date_of_death' => '2026-01-01', 'created_by' => $user->id]);

        return Application::create(['client_uuid' => $client->uuid,
            'beneficiary_uuid' => $beneficiary->uuid, 'relationship_id' => 1]);
    }

    public function test_cswdo_and_superadmin_without_staff_role_see_activity_and_both_charts(): void
    {
        $application = $this->application();
        foreach (['CSWDO', 'superadmin'] as $role) {
            $this->actingAs($this->user($role));
            Livewire::test(Dashboard::class)
                ->assertSee('Recently submitted applications')
                ->assertSee($application->tracking_no)
                ->assertSee(route('application.show', $application), false)
                ->assertSee('Pending')
                ->assertSee($application->created_at->format('M d, Y · g:i A'))
                ->assertSee('Applications Per Status')
                ->assertSee('Applications Per Relationship')
                ->assertDontSee('data-table', false);
            $this->get(route('dashboard'))->assertOk()->assertSee('Contact Us')->assertSee('Back to top');
        }
    }

    public function test_recent_list_is_limited_and_sorted_by_submission_then_uuid(): void
    {
        $this->actingAs($this->user('CSWDO'));
        $applications = collect(range(1, 22))->map(function ($index) {
            $application = $this->application();
            $application->forceFill(['created_at' => now()->startOfDay()->addMinutes(intdiv($index, 2))])->saveQuietly();

            return $application;
        });
        $expected = $applications->sortBy([['created_at', 'desc'], ['uuid', 'desc']])->take(20);
        $component = Livewire::test(Dashboard::class);
        $this->assertSame($expected->pluck('uuid')->values()->all(),
            $component->viewData('recentApplications')->pluck('uuid')->values()->all());
        $component->assertSeeInOrder($expected->pluck('tracking_no')->all());
        foreach ($applications->whereNotIn('uuid', $expected->pluck('uuid')) as $excluded) {
            $component->assertDontSee($excluded->tracking_no);
        }
        $component->assertSet('applicationsPerStatusCount.pending', 22);
    }

    public function test_empty_state_and_poll_refresh_include_a_new_submission(): void
    {
        $this->actingAs($this->user('CSWDO'));
        $component = Livewire::test(Dashboard::class)->assertSee('No applications submitted yet');
        $application = $this->application();
        $component->call('loadData')->assertSee($application->tracking_no)
            ->assertDontSee('No applications submitted yet')
            ->assertSet('applicationsPerStatusCount.pending', 1)
            ->assertDispatched('refresh-chart');
    }

    public function test_citizen_keeps_their_table_and_cannot_receive_staff_activity(): void
    {
        $citizen = $this->user();
        $own = $this->application($citizen);
        $other = $this->application();
        $this->actingAs($citizen);
        $component = Livewire::test(Dashboard::class)->assertSee('data-table', false)
            ->assertSee($own->tracking_no)->assertDontSee($other->tracking_no)
            ->assertDontSee('Recently submitted applications')->assertDontSee('Applications Per Status');
        $this->assertCount(0, $component->viewData('recentApplications'));
        $component->call('loadData')->assertDontSee($other->tracking_no);
        $this->get(route('dashboard'))->assertOk()->assertSee('Contact Us')->assertSee('Back to top');
    }

    public function test_footer_email_uses_settings_then_configuration_then_default(): void
    {
        config(['services.cswdo.department_email' => 'configured@example.test']);
        $settings = SystemSetting::create(['uuid' => (string) Str::uuid(), 'department_email' => 'settings@example.test']);
        $this->assertStringContainsString('mailto:settings@example.test', Blade::render('<x-page-footer />'));
        $settings->update(['department_email' => ' ']);
        $this->assertStringContainsString('mailto:configured@example.test', Blade::render('<x-page-footer />'));
        config(['services.cswdo.department_email' => '']);
        $this->assertStringContainsString('mailto:cswdotaguigcity@gmail.com', Blade::render('<x-page-footer />'));
        $settings->delete();
        $this->assertStringContainsString('mailto:cswdotaguigcity@gmail.com', Blade::render('<x-page-footer />'));
    }
}
