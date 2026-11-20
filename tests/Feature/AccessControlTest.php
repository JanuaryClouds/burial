<?php

namespace Tests\Feature;

use App\Livewire\Application\Index;
use App\Livewire\Application\Search;
use App\Livewire\Application\Show;
use App\Livewire\Client\Create;
use App\Livewire\Dashboard;
use App\Models\Application;
use App\Models\Beneficiary;
use App\Models\Client;
use App\Models\Closure;
use App\Models\FuneralAssistanceType;
use App\Models\Recommendation;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowHistory;
use App\Services\Auth\InvitationService;
use App\Services\Auth\UserService;
use App\Services\RemarkService;
use App\Services\RoleReconciliationService;
use App\Services\RoleService;
use App\Services\WorkflowVersionService;
use App\View\Components\PageNavigation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'access_testing',
            'database.connections.access_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true],
            'cache.default' => 'array', 'session.driver' => 'array', 'app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
            Cache::put($lookup, collect(), 3600);
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Artisan::call('migrate', ['--database' => 'access_testing', '--force' => true]);
        foreach (['sexes', 'religions', 'relationships', 'mode_of_assistances'] as $table) {
            DB::table($table)->insert(['name' => $table]);
        }
        foreach (['Create Interview', 'Create Assessment', 'Create Recommendation', 'Create Rejection', 'Create Referral', 'Create Closure', 'View Report', 'Create Report', 'Workflow Admin', 'Workflow Treasury', 'Workflow Accounting'] as $name) {
            Permission::findOrCreate($name, 'web');
        }
        foreach (['staff', 'CSWDO', 'superadmin'] as $role) {
            Role::findOrCreate($role, 'web');
        }
        $this->admin = $this->user(['superadmin']);
        $this->actingAs($this->admin);
    }

    protected function tearDown(): void
    {
        DB::purge('access_testing');
        parent::tearDown();
    }

    public function test_record_table_rendering_uses_no_queries_and_preserves_empty_columns(): void
    {
        foreach ([
            Index::class, \App\Livewire\Client\Index::class, \App\Livewire\Beneficiary\Index::class,
            \App\Livewire\User\Index::class, \App\Livewire\Role\Index::class, \App\Livewire\Workflow\Index::class,
            \App\Livewire\DocumentRequirement\Index::class, \App\Livewire\FuneralAssistanceType\Index::class,
            \App\Livewire\ModeOfAssistance\Index::class, \App\Livewire\ActivityLog\Index::class,
        ] as $component) {
            $table = Livewire::test($component);
            $columns = $table->get('columns');
            $this->assertNotEmpty($columns);
            $this->assertTrue($columns->contains('data', 'show_route'));
            DB::enableQueryLog();
            DB::flushQueryLog();
            $html = Blade::render(
                '<x-data-table data-table-id="query-check" :columns="$columns" :data="$rows" :card-fields="$fields" />',
                ['columns' => $columns, 'rows' => collect(), 'fields' => ['primary' => 'name']]
            );
            $this->assertSame([], DB::getQueryLog(), 'Presentation must not query the database.');
            $this->assertStringContainsString('data-responsive-data-table', $html);
            DB::disableQueryLog();
        }
    }

    public function test_application_index_refresh_fetches_the_index_once(): void
    {
        $this->application();
        $index = Livewire::test(Index::class)->instance();
        $budgets = [];
        for ($refresh = 0; $refresh < 2; $refresh++) {
            DB::enableQueryLog();
            DB::flushQueryLog();
            $index->loadData();
            $queries = collect(DB::getQueryLog());
            $budgets[] = $queries->count();
            $indexReads = $queries->filter(fn ($query) => str_starts_with($query['query'], 'select * from "applications"'));
            $this->assertCount(1, $indexReads, 'Cards and charts must reuse the parent index collection.');
            DB::disableQueryLog();
        }
        $this->assertSame($budgets[0], $budgets[1]);
    }

    public function test_legacy_record_indexes_use_responsive_tables_without_ajax_refetching(): void
    {
        foreach (['interview.index', 'referral.index'] as $route) {
            $this->get(route($route))->assertOk()
                ->assertSee('data-responsive-data-table', false)
                ->assertSee('data-columns=', false)
                ->assertDontSee('data-src="allData"', false);
        }
    }

    private function user(array $roles = [], array $permissions = []): User
    {
        $user = User::create(['first_name' => 'Access', 'last_name' => 'User', 'email' => Str::uuid().'@example.test', 'password' => 'test-password', 'is_active' => true]);
        foreach ($roles as $role) {
            $user->assignRole(Role::findOrCreate($role, 'web'));
        }
        foreach ($permissions as $permission) {
            $user->givePermissionTo(Permission::findOrCreate($permission, 'web'));
        }

        return $user;
    }

    private function application(?Workflow $workflow = null): Application
    {
        $owner = $this->user();
        $client = Client::create(['user_id' => $owner->id, 'date_of_birth' => '1980-01-01', 'contact_number' => '09123456789']);
        $beneficiary = Beneficiary::create(['first_name' => 'Access', 'last_name' => 'Beneficiary', 'sex_id' => 1, 'religion_id' => 1,
            'date_of_birth' => '1960-01-01', 'date_of_death' => '2026-01-01', 'created_by' => $owner->id]);
        $application = Application::create(['client_uuid' => $client->uuid, 'beneficiary_uuid' => $beneficiary->uuid, 'relationship_id' => 1]);
        if ($workflow) {
            $type = FuneralAssistanceType::create(['name' => Str::uuid(), 'description' => 'Assistance']);
            $run = Recommendation::create(['application_uuid' => $application->uuid, 'claimant_assignment_uuid' => $application->current_claimant_assignment_uuid,
                'workflow_uuid' => $workflow->uuid, 'funeral_assistance_type_uuid' => $type->uuid, 'recommended_by' => $this->admin->id, 'mode_of_assistance_id' => 1]);
            $application->update(['current_recommendation_uuid' => $run->uuid]);
        }

        return $application;
    }

    private function workflow(): Workflow
    {
        $this->actingAs($this->admin);

        return app(WorkflowVersionService::class)->publish(['name' => 'Department workflow '.Str::uuid(), 'description' => 'Access testing', 'stages' => collect(['Admin', 'Treasury', 'Accounting'])->map(fn ($name) => [
            'name' => $name, 'description' => $name, 'permission_id' => (string) Permission::findByName('Workflow '.$name, 'web')->id,
        ])->all()]);
    }

    public function test_only_roleless_users_can_enter_citizen_creation_paths(): void
    {
        foreach ([[], ['staff'], ['CSWDO'], ['Treasury'], ['superadmin']] as $roles) {
            $user = $this->user($roles);
            foreach ([Client::class, Beneficiary::class, Application::class] as $model) {
                $this->assertSame($roles === [], $user->can('create', $model));
            }
            if ($roles) {
                $this->actingAs($user);
                foreach (['client.create', 'beneficiary.create', 'application.create'] as $route) {
                    $this->getJson(route($route))->assertForbidden();
                }
                Livewire::test(Create::class)->assertForbidden();
            }
        }
    }

    public function test_staff_only_and_department_users_cannot_browse_record_indexes_or_print(): void
    {
        $application = $this->application();
        foreach ([['staff'], ['staff', 'Treasury']] as $roles) {
            $this->actingAs($this->user($roles, ['Workflow Treasury']));
            foreach (['application.index', 'client.index', 'beneficiary.index'] as $route) {
                $this->getJson(route($route))->assertForbidden();
            }
            Livewire::test(Index::class)->assertForbidden();
            foreach (['application.print', 'application.certificate'] as $route) {
                $this->getJson(route($route, $application))->assertForbidden();
            }
            $this->getJson(route('client.show', $application->client_uuid))->assertForbidden();
            $this->getJson(route('beneficiary.show', $application->beneficiary_uuid))->assertForbidden();
        }
    }

    public function test_office_action_permission_requires_cswdo_and_supports_inheritance(): void
    {
        $role = Role::findOrCreate('Assessor', 'web');
        $role->givePermissionTo('Create Assessment');
        $user = $this->user(['staff', 'Assessor']);
        $this->assertFalse($user->canOfficeAction('Create Assessment'));
        $user->assignRole('CSWDO');
        $this->assertTrue($user->canOfficeAction('Create Assessment'));
        $this->assertFalse($user->canOfficeAction('Create Recommendation'));
        $this->assertTrue($this->admin->canOfficeAction('Create Recommendation'));
    }

    public function test_workflow_access_matches_current_next_and_return_targets_only(): void
    {
        $workflow = $this->workflow();
        $application = $this->application($workflow);
        $stages = $workflow->stages()->orderBy('position')->get();
        $admin = $this->user(['staff', 'Admin'], ['Workflow Admin']);
        $treasury = $this->user(['staff', 'Treasury'], ['Workflow Treasury']);
        $accounting = $this->user(['staff', 'Accounting'], ['Workflow Accounting']);
        $this->assertTrue($admin->can('view', $application));
        $this->assertFalse($treasury->can('view', $application));
        $application->update(['current_workflow_stage_uuid' => $stages[0]->uuid]);
        $application->unsetRelation('workflowStage');
        $this->assertTrue($admin->can('view', $application));
        $this->assertTrue($treasury->can('view', $application));
        $this->assertFalse($accounting->can('view', $application));
        $application->update(['current_workflow_stage_uuid' => $stages[1]->uuid]);
        $application->unsetRelation('workflowStage');
        $this->assertTrue($admin->can('view', $application));
        $this->assertTrue($treasury->can('view', $application));
        $this->assertTrue($accounting->can('view', $application));
        $this->assertFalse($this->user(['staff'], ['Workflow Unrelated'])->can('view', $application));
    }

    public function test_paused_and_closed_applications_retain_stage_based_read_access(): void
    {
        $workflow = $this->workflow();
        $application = $this->application($workflow);
        $user = $this->user(['staff', 'Admin'], ['Workflow Admin']);
        $workflow->update(['is_active' => false]);
        Closure::create(['application_uuid' => $application->uuid, 'reason' => 'Closed', 'closed_by' => $this->admin->id, 'closed_at' => now()]);
        $this->assertTrue($user->can('view', $application->fresh()));
        $this->assertFalse($application->fresh()->isActive());
    }

    public function test_exact_tracking_and_scanned_search_hide_inaccessible_and_stale_results(): void
    {
        $application = $this->application($this->workflow());
        $other = $this->application();
        $this->actingAs($this->user(['staff', 'Admin'], ['Workflow Admin']));
        $component = Livewire::test(Search::class)->set('code', $application->tracking_no)->call('search')->assertHasNoErrors();
        $this->assertSame($application->uuid, $component->get('application')->uuid);
        $component->set('code', $application->qr_code)->call('search')->assertHasNoErrors();
        $component->set('code', $other->tracking_no)->call('search')->assertHasErrors('code')->assertSet('application', null)->assertSet('qrCode', null);
        $component->set('code', 'UNKNOWN')->call('search')->assertHasErrors('code')->assertSet('application', null);
    }

    public function test_department_show_is_read_only_and_reauthorizes_after_permission_revocation(): void
    {
        $application = $this->application($this->workflow());
        $user = $this->user(['staff', 'Admin'], ['Workflow Admin']);
        $this->actingAs($user);
        $component = Livewire::test(Show::class, ['application' => $application])
            ->assertSee('Application Summary')->assertSee('Assessment')->assertSee('Recommendation')->assertSee('Documents')
            ->assertDontSee('Printable Documents')->assertDontSee('Show Client')->assertDontSee('Show Beneficiary')->assertDontSee('Make an Assessment');
        $user->revokePermissionTo('Workflow Admin');
        $component->call('refreshApplication')->assertForbidden();
    }

    public function test_cswdo_panel_follows_workflow_history(): void
    {
        $application = $this->application($this->workflow());
        $this->actingAs($this->user(['staff', 'CSWDO']));
        Livewire::test(Show::class, ['application' => $application])->assertSeeInOrder(['id="workflow-history"', 'id="workflow-history-create-form"', 'id="printable-documents"'], false);
    }

    public function test_department_poll_reuses_eligibility_query_and_checks_permissions_again_next_poll(): void
    {
        $application = $this->application($this->workflow());
        $user = $this->user(['staff', 'Admin'], ['Workflow Admin']);
        $this->actingAs($user);
        $page = Livewire::test(Show::class, ['application' => $application]);
        DB::enableQueryLog();
        try {
            $page->call('$refresh')->assertSee('Application Summary');
            $stageChecks = collect(DB::getQueryLog())->filter(fn ($query) => str_starts_with($query['query'], 'select exists(')
                && str_contains($query['query'], 'from "workflow_stages"'));
            $this->assertCount(1, $stageChecks, 'Repeated authorization of one snapshot should reuse its stage eligibility result.');
        } finally {
            DB::disableQueryLog();
            DB::flushQueryLog();
        }
        $user->revokePermissionTo('Workflow Admin');
        $page->call('$refresh')->assertForbidden();
    }

    public function test_departmental_remarks_are_limited_to_current_workflow_history(): void
    {
        $workflow = $this->workflow();
        $application = $this->application($workflow);
        $run = $application->activeRecommendation;
        $history = WorkflowHistory::create(['recommendation_uuid' => $run->uuid, 'to_stage_uuid' => $workflow->stages()->first()->uuid, 'date_in' => now(), 'date_out' => now(), 'processed_by' => $this->admin->id]);
        $this->actingAs($this->user(['staff', 'Admin'], ['Workflow Admin']));
        $service = app(RemarkService::class);
        $this->assertTrue($service->canRead($run));
        $this->assertFalse($service->canWrite($run));
        $this->assertTrue($service->canWrite($history));
        $service->store(['content' => 'Review the submitted document.'], $history);
        $this->assertSame(1, $history->remarks()->count());
    }

    public function test_role_reconciliation_is_dry_run_by_default_and_preserves_collision_memberships(): void
    {
        $staff = Role::findByName('staff', 'web');
        $staff->givePermissionTo('Create Closure', 'Workflow Treasury');
        $legacy = Role::findOrCreate('lgu admin', 'web');
        $legacy->givePermissionTo('Workflow Admin');
        $canonical = Role::findOrCreate('Admin', 'web');
        $canonical->givePermissionTo('Workflow Treasury');
        $user = $this->user(['staff', 'lgu admin']);
        $user->givePermissionTo('Create Interview');
        $before = Role::count();
        $this->artisan('access:reconcile-roles')->assertSuccessful();
        $this->assertSame($before, Role::count());
        $this->assertCount(2, $staff->fresh()->permissions);
        app(RoleReconciliationService::class)->apply();
        $user->refresh();
        $this->assertTrue($user->hasRole('Admin'));
        $this->assertFalse($user->hasRole('lgu admin'));
        $this->assertFalse($user->hasRole('CSWDO'));
        $this->assertFalse($user->hasRole('Releasing'));
        $this->assertTrue($user->hasDirectPermission('Create Interview'));
        $this->assertCount(0, $staff->fresh()->permissions);
        $this->assertCount(2, $canonical->fresh()->permissions);
        $count = Role::count();
        app(RoleReconciliationService::class)->apply();
        $this->assertSame($count, Role::count());
    }

    private function profileData(User $user, array $roles): array
    {
        return ['firstName' => 'Updated', 'lastName' => 'User', 'email' => $user->email, 'contactNumber' => '09123456789', 'isActive' => true, 'roles' => $roles];
    }

    public function test_cms_creates_staff_without_automatic_cswdo_and_assigns_selected_roles(): void
    {
        $service = app(UserService::class);
        $email = Str::uuid().'@example.test';
        $invitation = app(InvitationService::class)->create($email, []);
        $this->assertDatabaseMissing('users', ['email' => $email]);
        $user = app(InvitationService::class)->accept($invitation->token, $this->invitationDetails());
        $this->assertTrue($user->hasRole('staff'));
        $this->assertFalse($user->hasRole('CSWDO'));
        $service->update(['roles' => ['CSWDO' => true]], $user);
        $this->assertTrue($user->fresh()->hasRole('CSWDO'));
        $service->update(['roles' => []], $user);
        $this->assertSame(['staff'], $user->fresh()->roles->pluck('name')->all());
    }

    public function test_nonadmin_cannot_grant_themselves_cswdo(): void
    {
        $user = $this->user(['staff']);
        $this->actingAs($user);
        $this->expectException(HttpException::class);
        app(UserService::class)->update($this->profileData($user, ['CSWDO' => true]), $user);
    }

    public function test_reserved_role_changes_are_rejected_server_side(): void
    {
        $this->expectException(HttpException::class);
        app(RoleService::class)->update(['name' => 'Not CSWDO', 'permissions' => []], Role::findByName('CSWDO', 'web'));
    }

    public function test_inherited_workflow_permission_revocation_and_new_recommendation_revoke_open_views(): void
    {
        $application = $this->application($this->workflow());
        $role = Role::findOrCreate('Department', 'web');
        $role->givePermissionTo('Workflow Admin');
        $user = $this->user(['staff', 'Department']);
        $this->actingAs($user);
        $view = Livewire::test(Show::class, ['application' => $application])->assertSee('Application Summary');
        $role->revokePermissionTo('Workflow Admin');
        $view->call('refreshApplication')->assertForbidden();
        $role->givePermissionTo('Workflow Admin');
        $view = Livewire::test(Show::class, ['application' => $application])->assertSee('Application Summary');
        $application->update(['current_recommendation_uuid' => null]);
        $view->call('refreshApplication')->assertForbidden();
    }

    public function test_cswdo_revocation_clears_dashboard_and_rejects_record_components(): void
    {
        $application = $this->application();
        $user = $this->user(['staff', 'CSWDO']);
        $this->actingAs($user);
        $dashboard = Livewire::test(Dashboard::class)->assertSee($application->tracking_no);
        $user->removeRole('CSWDO');
        $dashboard->call('loadData')->assertDontSee($application->tracking_no)->assertSee('no operational access');
        Livewire::test(\App\Livewire\Client\Index::class)->assertForbidden();
        Livewire::test(\App\Livewire\Beneficiary\Index::class)->assertForbidden();
    }

    public function test_office_forms_and_report_routes_require_office_membership_and_permissions(): void
    {
        $application = $this->application();
        $user = $this->user(['staff'], ['Create Assessment', 'View Report', 'Create Report']);
        $this->actingAs($user);
        Livewire::test(\App\Livewire\Assessment\Create::class, ['application' => $application])->assertForbidden();
        $this->getJson(route('report.index'))->assertForbidden();
        $user->assignRole('CSWDO');
        $user->revokePermissionTo('View Report');
        $this->getJson(route('report.index'))->assertForbidden();
        $this->assertTrue($user->can('create-reports'));
        $this->assertFalse($user->can('view-reports'));
    }

    public function test_role_reconciliation_stops_before_changes_on_inconsistent_guards(): void
    {
        $role = Role::findOrCreate('Legacy', 'web');
        $permission = Permission::findOrCreate('Foreign permission', 'api');
        DB::table(config('permission.table_names.role_has_permissions'))->insert(['role_id' => $role->id, 'permission_id' => $permission->id]);
        $before = Role::count();
        $this->artisan('access:reconcile-roles', ['--apply' => true])->assertFailed();
        $this->assertSame($before, Role::count());
        $this->assertNull(Role::where('name', 'Closer')->first());
    }

    public function test_cms_can_explicitly_select_office_and_action_roles_at_creation(): void
    {
        $role = Role::findOrCreate('Assessor', 'web');
        $role->givePermissionTo('Create Assessment');
        $email = Str::uuid().'@example.test';
        $invitation = app(InvitationService::class)->create($email, ['CSWDO', 'Assessor']);
        $this->assertDatabaseMissing('users', ['email' => $email]);
        $user = app(InvitationService::class)->accept($invitation->token, $this->invitationDetails());
        $this->assertTrue($user->hasAllRoles(['staff', 'CSWDO', 'Assessor']));
        $this->assertTrue($user->canOfficeAction('Create Assessment'));
        $this->assertFalse($user->canOfficeAction('Create Recommendation'));
    }

    public function test_cms_invitation_form_lists_cswdo_and_acceptance_saves_explicit_membership(): void
    {
        $email = Str::uuid().'@example.test';
        $this->get(route('user.invitations.index'))->assertOk()->assertSee('CSWDO');
        $invitation = app(InvitationService::class)->create($email, ['CSWDO']);
        $this->assertDatabaseMissing('users', ['email' => $email]);
        auth()->logout();
        $this->post(route('invitation.accept', $invitation->token), $this->invitationDetails())
            ->assertRedirect(route('admin.login'))->assertSessionHasNoErrors();
        $this->assertTrue(User::where('email', $email)->firstOrFail()->hasAllRoles(['staff', 'CSWDO']));
    }

    private function invitationDetails(): array
    {
        return ['first_name' => 'Office', 'last_name' => 'User', 'contact_number' => '09123456789',
            'password' => 'Owner password 9!', 'password_confirmation' => 'Owner password 9!'];
    }

    public function test_department_navigation_returns_to_search_instead_of_the_record_index(): void
    {
        $application = $this->application($this->workflow());
        $user = $this->user(['staff', 'Admin'], ['Workflow Admin']);
        $this->actingAs($user);
        $request = Request::create(route('application.show', $application));
        $route = clone Route::getRoutes()->getByName('application.show');
        $route->bind($request);
        $request->setRouteResolver(fn () => $route);
        $request->setUserResolver(fn () => $user);
        $navigation = new PageNavigation($request);
        $this->assertSame(route('application.search'), $navigation->parent['url']);
    }

    public function test_staff_only_dashboard_has_no_application_data(): void
    {
        $application = $this->application();
        $this->actingAs($this->user(['staff']));
        Livewire::test(Dashboard::class)->assertSee('no operational access')->assertDontSee($application->tracking_no)->assertDontSee('Apply');
    }
}
