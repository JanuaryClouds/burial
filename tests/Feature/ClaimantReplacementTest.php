<?php

namespace Tests\Feature;

use App\Http\Controllers\ImageController;
use App\Livewire\Application\ChangeClaimant;
use App\Livewire\Application\Show;
use App\Livewire\Interview\Create;
use App\Livewire\Interview\Index;
use App\Models\Address;
use App\Models\Application;
use App\Models\ApplicationClaimantAssignment;
use App\Models\Assessment;
use App\Models\Beneficiary;
use App\Models\Client;
use App\Models\ClientDemographic;
use App\Models\ClientSocialInfo;
use App\Models\DocumentRequirement;
use App\Models\FuneralAssistanceType;
use App\Models\Interview;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\ApplicationProcessingService;
use App\Services\ApplicationService;
use App\Services\AssessmentService;
use App\Services\ChequeService;
use App\Services\ClaimantAssignmentAudit;
use App\Services\ClaimantReplacementService;
use App\Services\ImageService;
use App\Services\InterviewService;
use App\Services\RecommendationService;
use App\Services\RemarkService;
use App\Services\WorkflowHistoryService;
use App\Services\WorkflowVersionService;
use Barryvdh\DomPDF\PDF;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class ClaimantReplacementTest extends TestCase
{
    private ?string $mysqlDatabase = null;

    private User $admin;

    private int $relationship;

    protected function setUp(): void
    {
        parent::setUp();
        $connection = ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true];
        if (getenv('CLAIMANT_TEST_MYSQL') === '1') {
            $this->mysqlDatabase = 'burial_claimant_test_'.bin2hex(random_bytes(6));
            $connection = config('database.connections.mysql');
            config(['database.connections.claimant_server' => [...$connection, 'database' => null, 'url' => null]]);
            DB::connection('claimant_server')->statement('CREATE DATABASE `'.$this->mysqlDatabase.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            $connection['database'] = $this->mysqlDatabase;
            $connection['url'] = null;
        }
        config([
            'database.default' => 'claimant_testing',
            'database.connections.claimant_testing' => $connection,
            'cache.default' => 'array', 'session.driver' => 'array',
            'app.key' => 'base64:'.base64_encode(str_repeat('c', 32)),
        ]);
        foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
            Cache::put($lookup, collect(), 3600);
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Artisan::call('migrate', ['--database' => 'claimant_testing', '--force' => true]);
        foreach (['sexes', 'religions', 'nationalities', 'civil_statuses', 'educations', 'relationships', 'mode_of_assistances'] as $table) {
            DB::table($table)->insert(['name' => $table]);
        }
        foreach (['Create Rejection', 'Create Referral', 'Create Closure', 'Create Interview', 'Create Assessment', 'Create Recommendation'] as $name) {
            Permission::findOrCreate($name, 'web');
        }
        $this->relationship = DB::table('relationships')->value('id');
        SystemSetting::create(['uuid' => (string) Str::uuid(), 'claimant_change_limit' => 1]);
        $this->admin = $this->user('Admin');
        $this->admin->assignRole(Role::findOrCreate('superadmin', 'web'));
        $this->actingAs($this->admin);
    }

    protected function tearDown(): void
    {
        DB::purge('claimant_testing');
        if ($this->mysqlDatabase && preg_match('/^burial_claimant_test_[a-f0-9]{12}$/', $this->mysqlDatabase)) {
            DB::connection('claimant_server')->statement('DROP DATABASE `'.$this->mysqlDatabase.'`');
            DB::purge('claimant_server');
        }
        parent::tearDown();
    }

    private function user(string $name): User
    {
        return User::create(['first_name' => $name, 'last_name' => 'Claimant', 'email' => Str::uuid().'@example.test', 'password' => 'test-password']);
    }

    private function draft(?User $user = null): Client
    {
        $user ??= $this->user('Client');
        $client = Client::create(['user_id' => $user->id, 'date_of_birth' => '1980-01-01', 'contact_number' => '09123456789']);
        ClientDemographic::create(['client_uuid' => $client->uuid, 'sex_id' => 1, 'nationality_id' => 1, 'religion_id' => 1]);
        ClientSocialInfo::create(['client_uuid' => $client->uuid, 'civil_id' => 1, 'education_id' => 1]);
        Address::create(['addressable_type' => Client::class, 'addressable_id' => $client->uuid,
            'region_code' => '13', 'region_name' => 'NCR', 'municipality_code' => '137607', 'municipality_name' => 'Taguig',
            'barangay_code' => '137607001', 'barangay_name' => 'Bagumbayan', 'street' => 'Main Street', 'house_number' => '1']);

        return $client;
    }

    private function application(?Client $client = null): Application
    {
        $client ??= $this->draft();
        $beneficiary = Beneficiary::create(['first_name' => 'Recorded', 'last_name' => 'Beneficiary', 'sex_id' => 1, 'religion_id' => 1,
            'date_of_birth' => '1960-01-01', 'date_of_death' => '2026-01-01', 'created_by' => $client->user_id]);

        return Application::create(['client_uuid' => $client->uuid, 'beneficiary_uuid' => $beneficiary->uuid, 'relationship_id' => $this->relationship]);
    }

    private function replace(Application $application, ?Client $client = null): ApplicationClaimantAssignment
    {
        return app(ClaimantReplacementService::class)->replace($application->uuid, [
            'client_uuid' => ($client ?? $this->draft())->uuid, 'relationship_id' => $this->relationship,
            'reason' => 'Original claimant unavailable', 'expected_assignment_uuid' => $application->current_claimant_assignment_uuid,
        ]);
    }

    private function interview(Application $application): Interview
    {
        $interview = app(InterviewService::class)->store(['schedule' => now()->addDay()->toDateTimeString()], $application->client_uuid, $application->current_claimant_assignment_uuid);

        return app(InterviewService::class)->done($interview->uuid);
    }

    private function assessment(Application $application): Assessment
    {
        return app(AssessmentService::class)->store(['expected_assignment_uuid' => $application->current_claimant_assignment_uuid,
            'problem_presented' => 'Funeral costs', 'swa' => 'Needs assistance'], $application->uuid);
    }

    public function test_replacement_preserves_history_and_requires_entirely_fresh_processing(): void
    {
        $application = $this->application();
        $oldInterview = $this->interview($application);
        $oldAssessment = $this->assessment($application);
        $permission = Permission::findOrCreate('Workflow Verification', 'web');
        $workflow = app(WorkflowVersionService::class)->publish(['name' => 'Assistance', 'description' => 'Process', 'stages' => [
            ['name' => 'Verification', 'description' => 'Verify', 'permission_id' => (string) $permission->id,
                'custom_fields' => [['name' => 'verification_reference', 'label' => 'Verification reference', 'input_type' => 'text', 'required' => true]]],
        ]]);
        $type = FuneralAssistanceType::create(['name' => 'Burial', 'description' => 'Burial', 'workflow_uuid' => $workflow->uuid]);
        $run = app(RecommendationService::class)->store(['workflow_uuid' => $workflow->uuid, 'funeral_assistance_type_uuid' => $type->uuid,
            'amount_extended' => 1000, 'mode_of_assistance_id' => 1, 'expected_recommendation_uuid' => null,
            'expected_assignment_uuid' => $application->current_claimant_assignment_uuid], $application->uuid);
        $stage = $workflow->stages()->first();
        $field = $stage->customFields()->first();
        $history = app(WorkflowHistoryService::class)->store(['recommendation_uuid' => $run->uuid, 'expected_stage_uuid' => null,
            'to_stage_uuid' => $stage->uuid, 'date_in' => now()->toDateTimeString(), 'date_out' => now()->toDateTimeString(),
            'remark' => 'Original verification note', 'custom_values' => [$field->uuid => 'REF-001']]);
        $newClient = $this->draft();
        $draftInterview = Interview::create(['client_uuid' => $newClient->uuid, 'status' => 'done', 'schedule' => now()]);
        $identity = $application->only(['tracking_no', 'qr_code', 'beneficiary_uuid']);
        $new = $this->replace($application, $newClient);
        $application->refresh();
        $this->assertSame($identity, $application->only(array_keys($identity)));
        $this->assertSame($newClient->uuid, $application->client_uuid);
        $this->assertNull($application->current_recommendation_uuid);
        $this->assertNull($application->current_workflow_stage_uuid);
        $this->assertNull($application->assessment);
        $this->assertFalse($application->finishedInterview());
        $application->load(['currentInterviews', 'referral', 'rejection', 'cancellation', 'closure', 'workflowStage']);
        $this->assertSame('pending', $application->currentStatus()['label']);
        $this->assertNotNull($oldAssessment->fresh());
        $this->assertNotNull($oldInterview->fresh());
        $this->assertNotNull($history->fresh());
        $this->assertNotNull($run->fresh());
        $this->assertSame('REF-001', $history->customValues()->first()->value);
        $this->assertSame('Original verification note', $history->remarks()->first()->content);
        $this->assertNull($draftInterview->fresh()->claimant_assignment_uuid);
        $this->assertSame(2, $new->sequence);
        $this->assertSame([], app(ClaimantAssignmentAudit::class)->inspect());
        try {
            app(WorkflowHistoryService::class)->store(['recommendation_uuid' => $run->uuid, 'expected_stage_uuid' => null,
                'to_stage_uuid' => $workflow->stages()->first()->uuid, 'date_in' => now()->toDateTimeString(), 'date_out' => now()->toDateTimeString()]);
            $this->fail('Previous claimant workflow advanced.');
        } catch (ValidationException) {
        }
        try {
            app(RemarkService::class)->store(['content' => 'Old cycle edit'], $history);
            $this->fail('Previous claimant history was modified.');
        } catch (ValidationException) {
        }
        $this->interview($application);
        $application->refresh();
        $this->assertTrue($application->finishedInterview());
        $this->assertNotSame($oldAssessment->uuid, $this->assessment($application)->uuid);
        $newRun = app(RecommendationService::class)->store(['workflow_uuid' => $workflow->uuid, 'funeral_assistance_type_uuid' => $type->uuid,
            'amount_extended' => 1500, 'mode_of_assistance_id' => 1, 'expected_recommendation_uuid' => null,
            'expected_assignment_uuid' => $application->current_claimant_assignment_uuid], $application->uuid);
        $this->assertSame($new->uuid, $newRun->claimant_assignment_uuid);
        $this->assertNotSame($run->uuid, $newRun->uuid);
        $this->assertNull($application->fresh()->current_workflow_stage_uuid);
    }

    public function test_current_and_former_claimants_can_view_but_only_current_can_cancel(): void
    {
        $old = $this->draft();
        $application = $this->application($old);
        $new = $this->draft();
        $this->replace($application, $new);
        $application->refresh()->load('client');
        foreach ([$old->user_id, $new->user_id] as $userId) {
            $user = User::findOrFail($userId);
            $this->assertTrue(Gate::forUser($user)->allows('view', $application));
            $this->assertTrue(Application::index($userId)->whereKey($application->uuid)->exists());
            $this->assertTrue(Gate::forUser($user)->allows('view', $application->beneficiary()->first()));
        }
        $this->assertFalse(Gate::forUser(User::findOrFail($old->user_id))->allows('cancel', $application));
        $this->assertTrue(Gate::forUser(User::findOrFail($new->user_id))->allows('cancel', $application));
        $this->assertFalse(Gate::forUser($this->user('Unrelated'))->allows('view', $application));
        $this->assertFalse(Gate::forUser(User::findOrFail($old->user_id))->allows('update', $old));
        $this->assertFalse(Client::unused()->whereKey($old->uuid)->exists());
    }

    public function test_stale_forms_cannot_assess_change_outcome_or_issue_cheques(): void
    {
        $application = $this->application();
        $expected = $application->current_claimant_assignment_uuid;
        $this->replace($application);
        foreach ([
            fn () => $this->assessment($application),
            fn () => app(ApplicationProcessingService::class)->outcome($application->uuid, $expected, 'cancel', ['reason' => 'Stale']),
            fn () => app(ChequeService::class)->save($application->uuid, $expected, ['obr_number' => 'OBR', 'status' => 'issued']),
            fn () => $this->replace($application),
        ] as $write) {
            try {
                $write();
                $this->fail('Stale writer was accepted.');
            } catch (ValidationException $e) {
                $this->assertNotEmpty($e->errors());
            }
        }
        $this->assertSame(0, Assessment::count());
        $this->assertSame(0, DB::table('cheques')->count());
        $this->assertSame(0, DB::table('cancellations')->count());
    }

    public function test_limit_zero_one_and_increased_limit_are_enforced(): void
    {
        $application = $this->application();
        SystemSetting::query()->update(['claimant_change_limit' => 0]);
        try {
            $this->replace($application);
            $this->fail('Disabled change accepted.');
        } catch (ValidationException) {
        }
        SystemSetting::query()->update(['claimant_change_limit' => 1]);
        $this->replace($application);
        try {
            $this->replace($application->fresh());
            $this->fail('Limit exceeded.');
        } catch (ValidationException) {
        }
        SystemSetting::query()->update(['claimant_change_limit' => 2]);
        $this->replace($application->fresh());
        $this->assertSame(3, $application->claimantAssignments()->count());
    }

    public function test_incomplete_used_and_same_account_drafts_are_rejected(): void
    {
        $client = $this->draft();
        $application = $this->application($client);
        $incomplete = Client::create(['user_id' => $this->user('Incomplete')->id, 'date_of_birth' => '1980-01-01', 'contact_number' => '1']);
        $used = $this->application()->client()->first();
        foreach ([$incomplete, $used, $this->draft(User::findOrFail($client->user_id)), $client] as $candidate) {
            try {
                $this->replace($application, $candidate);
                $this->fail('Ineligible draft accepted.');
            } catch (ValidationException) {
            }
        }
        $this->assertSame(1, $application->claimantAssignments()->count());
    }

    public function test_issued_and_claimed_cheques_block_but_cancelled_unclaimed_cheques_do_not(): void
    {
        foreach ([['issued', null, true], ['claimed', '2026-01-01', true], ['cancelled', '2026-01-01', true], ['cancelled', null, false]] as [$status, $claimed, $blocked]) {
            $application = $this->application();
            DB::table('cheques')->insert(['uuid' => (string) Str::uuid(), 'application_uuid' => $application->uuid, 'obr_number' => Str::uuid(), 'status' => $status, 'date_claimed' => $claimed]);
            try {
                $this->replace($application);
                $this->assertFalse($blocked);
            } catch (ValidationException) {
                $this->assertTrue($blocked);
            }
        }
    }

    public function test_terminal_applications_and_direct_unauthorized_livewire_actions_are_blocked(): void
    {
        foreach (['cancel', 'reject', 'refer'] as $outcome) {
            $application = $this->application();
            app(ApplicationProcessingService::class)->outcome($application->uuid, $application->current_claimant_assignment_uuid, $outcome, ['reason' => 'Terminal', 'referred_to' => 'Agency']);
            $this->assertFalse(Gate::allows('changeClaimant', $application));
        }
        $application = $this->application();
        $this->actingAs($this->user('Unauthorized'));
        $this->withoutExceptionHandling();
        $this->expectException(AuthorizationException::class);
        Livewire::test(ChangeClaimant::class, ['application' => $application]);
    }

    public function test_current_cycle_requires_interview_before_assessment_and_duplicate_assessment_is_blocked(): void
    {
        $application = $this->application();
        try {
            $this->assessment($application);
            $this->fail('Assessment before interview accepted.');
        } catch (AuthorizationException) {
        }
        $this->interview($application);
        $this->assessment($application);
        try {
            $this->assessment($application);
            $this->fail('Duplicate assessment accepted.');
        } catch (AuthorizationException) {
        }
        $this->assertSame(1, Assessment::count());
    }

    public function test_failure_rolls_back_assignment_end_and_new_assignment(): void
    {
        $application = $this->application();
        $expected = $application->current_claimant_assignment_uuid;
        $draft = $this->draft();
        $event = 'eloquent.created: '.ApplicationClaimantAssignment::class;
        Event::listen($event, function ($assignment) {
            if ($assignment->sequence > 1) {
                throw new \RuntimeException('Simulated write failure');
            }
        });
        try {
            try {
                $this->replace($application, $draft);
                $this->fail('Expected simulated failure.');
            } catch (\RuntimeException $e) {
                $this->assertSame('Simulated write failure', $e->getMessage());
            }
        } finally {
            Event::forget($event);
        }
        $this->assertSame($expected, $application->fresh()->current_claimant_assignment_uuid);
        $this->assertNull(ApplicationClaimantAssignment::findOrFail($expected)->ended_at);
        $this->assertSame(1, $application->claimantAssignments()->count());
        $this->assertFalse($draft->isUsed());
    }

    public function test_livewire_replacement_action_and_stale_token_are_enforced(): void
    {
        $application = $this->application();
        $draft = $this->draft();
        Livewire::test(ChangeClaimant::class, ['application' => $application])
            ->assertSee('Confirm Claimant Change')->assertDontSee('wire:confirm', false)
            ->set('search', $draft->user()->value('email'))->assertSee('Claimant')
            ->set('clientUuid', $draft->uuid)->set('relationshipId', $this->relationship)->set('reason', 'Unavailable')
            ->call('save')->assertHasNoErrors()->assertRedirect(route('application.show', $application));
        $this->assertSame($draft->uuid, $application->fresh()->client_uuid);
    }

    public function test_document_access_is_scoped_to_application_and_retained_for_former_client(): void
    {
        $application = $this->application();
        $former = User::findOrFail($application->client()->value('user_id'));
        $this->replace($application);
        DocumentRequirement::create(['name' => 'Death Certificate', 'source' => 'Registry']);
        $images = \Mockery::mock(ImageService::class);
        $images->shouldReceive('get')->once()->with((app()->isLocal() ? 'test-' : '').$application->tracking_no.'-death-certificate.jpg.enc')->andReturn('retained-image');
        $controller = new ImageController($images);
        $this->actingAs($former);
        $this->assertSame('retained-image', $controller->get($application->fresh(), 'death_certificate')->getContent());
        try {
            $controller->get($application->fresh(), 'another-application-death_certificate');
            $this->fail('Invalid document key accepted.');
        } catch (HttpException $e) {
            $this->assertSame(404, $e->getStatusCode());
        }
        $this->actingAs($this->user('Unrelated'));
        try {
            $controller->get($application->fresh(), 'death_certificate');
            $this->fail('Unrelated user read document.');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
    }

    public function test_backfill_is_preserving_and_ambiguous_data_blocks_ddl(): void
    {
        $application = $this->application();
        $this->interview($application);
        $this->assessment($application);
        $migration = require database_path('migrations/data/2026_10_26_120000_add_application_claimant_assignments.php');
        $migration->down();
        $this->assertSame([], app(ClaimantAssignmentAudit::class)->inspect());
        $migration->up();
        $application->refresh();
        $this->assertNotNull($application->assessment);
        $this->assertTrue($application->finishedInterview());
        $migration->down();
        DB::table('assessments')->insert(['uuid' => (string) Str::uuid(), 'application_uuid' => $application->uuid, 'swa' => 'Duplicate', 'problem_presented' => 'Duplicate']);
        try {
            $migration->up();
            $this->fail('Ambiguous backfill accepted.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('multiple assessments', $e->getMessage());
        }
        $this->assertFalse(Schema::hasTable('application_claimant_assignments'));
    }

    public function test_assignment_and_history_rendering_preload_relationships(): void
    {
        $application = $this->application();
        $this->replace($application);
        $violations = [];
        Model::handleLazyLoadingViolationUsing(function ($model, $relation) use (&$violations) {
            $violations[] = get_class($model).'.'.$relation;
        });
        try {
            Livewire::test(ChangeClaimant::class, ['application' => $application->fresh()])->assertSee('Change Claimant');
            $assignments = ApplicationClaimantAssignment::with(['interviews', 'assessments', 'recommendations.workflow', 'assignedBy', 'relationship'])->get();
            view('application.show.partials.claimant-history', ['claimantHistory' => $assignments])->render();
            Livewire::test(Show::class, ['application' => $application->fresh()])->assertSee('Claimant History');
            $this->actingAs(User::findOrFail($application->client()->value('user_id')));
            Livewire::test(Show::class, ['application' => $application->fresh()])->assertSee('Claimant History');
            $this->assertSame([], $violations);
        } finally {
            Model::handleLazyLoadingViolationUsing(null);
        }
    }

    public function test_print_uses_replacement_claimant_without_previous_assessment(): void
    {
        $application = $this->application();
        $this->interview($application);
        $this->assessment($application);
        $replacement = $this->draft($this->user('Replacement'));
        $this->replace($application, $replacement);
        $builder = \Mockery::mock(PDF::class);
        $this->app->instance('dompdf.wrapper', $builder);
        $builder->shouldReceive('setOption', 'setPaper')->andReturnSelf();
        $builder->shouldReceive('stream')->once()->andReturn(response('fresh-pdf'));
        $builder->shouldReceive('loadView')->once()->withArgs(function ($view, $data) use ($replacement) {
            $this->assertSame('pdf.gis-form', $view);
            $this->assertSame($replacement->uuid, $data['client']->uuid);
            $this->assertSame('N/A', $data['data']['assessment']['swa']);
            $this->assertNull($data['recommendation']);

            return true;
        })->andReturn($builder);
        $application = $application->fresh();
        $application->preventsLazyLoading = true;
        $this->assertSame('fresh-pdf', app(ApplicationService::class)->print($application)->getContent());
    }

    public function test_staff_can_schedule_and_complete_current_cycle_interview_from_livewire(): void
    {
        $application = $this->application();
        $this->replace($application);
        $application->refresh();
        $client = $application->client()->first();
        Livewire::test(Create::class, ['client' => $client])
            ->set('schedule', now()->addDay()->format('Y-m-d\TH:i'))
            ->call('save')->assertHasNoErrors();
        $interview = $application->currentInterviews()->sole();
        $this->assertSame('scheduled', $interview->status);
        $this->assertFalse(Gate::allows('create', [Interview::class, $client]));
        Livewire::test(Index::class, ['client' => $client, 'assignmentUuid' => $application->current_claimant_assignment_uuid])
            ->call('markAsDone', $interview->uuid)->assertHasNoErrors();
        $this->assertTrue($application->finishedInterview());
        $this->assertFalse(Gate::allows('create', [Interview::class, $client]));
        Livewire::test(Create::class, ['client' => $client])->assertDontSee('Schedule Interview');
        try {
            app(InterviewService::class)->store(['schedule' => now()->addDays(2)->toDateTimeString()], $client->uuid, $application->current_claimant_assignment_uuid);
            $this->fail('A second interview was scheduled after completion.');
        } catch (AuthorizationException) {
        }
        $this->assertSame(1, $application->currentInterviews()->count());
    }

    public function test_application_show_query_budget(): void
    {
        $application = $this->application();
        $this->interview($application);
        $this->assessment($application);
        $permission = Permission::findOrCreate('Workflow Query Check', 'web');
        $workflow = app(WorkflowVersionService::class)->publish(['name' => 'Query check', 'description' => 'Process', 'stages' => [
            ['name' => 'Verify', 'description' => 'Verify', 'permission_id' => (string) $permission->id],
        ]]);
        $type = FuneralAssistanceType::create(['name' => 'Burial', 'description' => 'Burial', 'workflow_uuid' => $workflow->uuid]);
        app(RecommendationService::class)->store(['workflow_uuid' => $workflow->uuid, 'funeral_assistance_type_uuid' => $type->uuid,
            'amount_extended' => 1000, 'mode_of_assistance_id' => 1, 'expected_recommendation_uuid' => null,
            'expected_assignment_uuid' => $application->current_claimant_assignment_uuid], $application->uuid);
        DB::enableQueryLog();
        try {
            $page = Livewire::test(Show::class, ['application' => $application->fresh()])->assertSee('Assessment');
            $queries = DB::getQueryLog();
            $unique = collect($queries)->map(fn ($query) => json_encode([$query['query'], $query['bindings']]))->unique()->count();
            $this->assertLessThanOrEqual(30, count($queries), 'Application rendering exceeded its query budget.');
            $this->assertLessThanOrEqual(2, count($queries) - $unique, 'Repeated queries returned to application rendering.');
            DB::flushQueryLog();
            $page->call('$refresh')->assertSee('Change Claimant')->assertDontSee('Search by name, email, or account ID');
            $queries = DB::getQueryLog();
            $unique = collect($queries)->map(fn ($query) => json_encode([$query['query'], $query['bindings']]))->unique()->count();
            $this->assertLessThanOrEqual(30, count($queries), 'Polling exceeded its query budget.');
            $this->assertLessThanOrEqual(2, count($queries) - $unique, 'Polling repeats identical queries.');
            $applicationReads = collect($queries)->filter(fn ($query) => preg_match('/^select \* from ["`]?applications["`]? where /i', $query['query']));
            $this->assertCount(1, $applicationReads, 'Polling should use the freshly hydrated application instead of fetching it again.');
            $this->actingAs($this->admin->fresh());
            DB::flushQueryLog();
            $this->get(route('application.show', $application))->assertOk();
            $queries = DB::getQueryLog();
            $unique = collect($queries)->map(fn ($query) => json_encode([$query['query'], $query['bindings']]))->unique()->count();
            $this->assertLessThanOrEqual(36, count($queries), 'Full application page exceeded its query budget.');
            $this->assertLessThanOrEqual(2, count($queries) - $unique, 'Full application page repeats identical queries.');
        } finally {
            DB::disableQueryLog();
            DB::flushQueryLog();
        }
    }

    public function test_tracker_slip_modal_preserves_its_bootstrap_root_during_polling(): void
    {
        $application = $this->application();
        $page = Livewire::test(Show::class, ['application' => $application]);
        $qrCode = $page->get('qrCode');
        $barcode = $page->get('barcode');
        $assertModal = function (string $html) use ($application): void {
            $this->assertSame(1, preg_match_all('/<div\b[^>]*\bid="tracker-slip-modal"[^>]*>/', $html, $matches));
            $this->assertStringContainsString('wire:ignore.self', $matches[0][0]);
            $this->assertStringContainsString('wire:key="tracker-slip-'.$application->uuid.'"', $matches[0][0]);
            $this->assertStringContainsString('data-bs-target="#tracker-slip-modal"', $html);
        };
        $assertModal($page->html());
        $application->client->user->update(['first_name' => 'Updated During Poll']);
        $page->call('$refresh')->assertSee('Updated During Poll');
        $assertModal($page->html());
        $this->assertSame($qrCode, $page->get('qrCode'));
        $this->assertSame($barcode, $page->get('barcode'));
    }

    public function test_claimant_change_page_has_separate_summary_and_form_and_requires_superadmin(): void
    {
        $application = $this->application();
        $this->actingAs($this->admin->fresh());
        $response = $this->get(route('application.change-claimant', $application));
        $response->assertOk()->assertSee('Application Summary')->assertSee('Search by name, email, or account ID');
        $this->assertSame(1, substr_count($response->getContent(), 'Search by name, email, or account ID'));
        $response->assertDontSee('wire:poll.30s="refreshApplication"', false);
        $unrelated = $this->user('Unrelated');
        $unrelated->update(['citizen_uuid' => (string) Str::uuid()]);
        $this->actingAs($unrelated->fresh());
        $this->withoutExceptionHandling();
        $this->expectException(AuthorizationException::class);
        $this->get(route('application.change-claimant', $application));
    }

    public function test_interview_summary_shows_only_latest_schedule_and_optional_remarks(): void
    {
        $application = $this->application();
        $old = Interview::create(['client_uuid' => $application->client_uuid, 'claimant_assignment_uuid' => $application->current_claimant_assignment_uuid,
            'schedule' => '2026-01-01 09:00:00', 'status' => 'canceled']);
        $latest = Interview::create(['client_uuid' => $application->client_uuid, 'claimant_assignment_uuid' => $application->current_claimant_assignment_uuid,
            'schedule' => '2026-02-01 10:00:00', 'status' => 'done']);
        Livewire::test(Index::class, ['client' => $application->client()->first(), 'assignmentUuid' => $application->current_claimant_assignment_uuid])
            ->assertSee('February 01, 2026')->assertDontSee('January 01, 2026');
        $latest->setAttribute('remarks', 'Verification completed.');
        $html = view('interview.partials.latest', ['latestInterview' => $latest, 'canComplete' => false])->render();
        $this->assertStringContainsString('Verification completed.', $html);
        $this->assertStringContainsString('Done', $html);
        $this->assertNotNull($old->fresh());
    }

    public function test_inline_interview_completion_refreshes_assessment_controls(): void
    {
        $application = $this->application();
        $interview = app(InterviewService::class)->store(['schedule' => now()->addDay()->toDateTimeString()], $application->client_uuid, $application->current_claimant_assignment_uuid);
        Livewire::test(Show::class, ['application' => $application->fresh()])
            ->assertSee('Mark As Done')->call('markAsDone', $interview->uuid)
            ->assertSee('Make an Assessment')->assertDontSee('Mark As Done');
        $this->assertSame('done', $interview->fresh()->status);
    }

    public function test_cached_display_state_cannot_bypass_fresh_processing_authorization(): void
    {
        $application = $this->application();
        $this->interview($application);
        $application->load(['currentInterviews', 'rejection', 'cancellation', 'referral', 'closure']);
        $this->assertTrue($application->isActive());
        app(ApplicationProcessingService::class)->outcome($application->uuid, $application->current_claimant_assignment_uuid, 'cancel', ['reason' => 'Concurrent cancellation']);
        try {
            $this->assessment($application);
            $this->fail('Cached active state bypassed the processing guard.');
        } catch (AuthorizationException) {
        }
        $this->assertSame(0, $application->assessment()->count());
    }

    public function test_mysql_competing_replacements_and_draft_consumption(): void
    {
        if (! $this->mysqlDatabase) {
            $this->markTestSkipped('Set CLAIMANT_TEST_MYSQL=1 to run against disposable MySQL databases.');
        }
        $application = $this->application();
        $draftA = $this->draft();
        $draftB = $this->draft();
        $this->race([[$application, $draftA], [$application, $draftB]]);
        $shared = $this->draft();
        $this->race([[$this->application(), $shared], [$this->application(), $shared]]);
    }

    private function race(array $pairs): void
    {
        $prefix = sys_get_temp_dir().DIRECTORY_SEPARATOR.'claimant-race-'.bin2hex(random_bytes(6));
        $workers = [];
        $files = [$prefix.'-go'];
        try {
            foreach ($pairs as $index => [$application, $draft]) {
                $ready = $prefix.'-'.$index;
                $files[] = $ready;
                $worker = new Process([PHP_BINARY, base_path('tests/Support/replace-claimant.php'), $this->mysqlDatabase,
                    (string) $this->admin->id, $application->uuid, $draft->uuid, (string) $this->relationship,
                    $application->current_claimant_assignment_uuid, $ready, $prefix.'-go'], base_path(), null, null, 30);
                $worker->start();
                $workers[] = $worker;
            }
            $deadline = microtime(true) + 15;
            while (! is_file($files[1]) || ! is_file($files[2])) {
                foreach ($workers as $worker) {
                    if (! $worker->isRunning()) {
                        $this->fail($worker->getErrorOutput().$worker->getOutput());
                    }
                }
                if (microtime(true) > $deadline) {
                    $this->fail('Worker startup timed out.');
                }
                usleep(10000);
            }
            file_put_contents($files[0], 'go');
            $results = [];
            foreach ($workers as $worker) {
                $worker->wait();
                $this->assertTrue($worker->isSuccessful(), $worker->getErrorOutput().$worker->getOutput());
                $results[] = trim($worker->getOutput());
            }
            sort($results);
            $this->assertSame(['rejected', 'replaced'], $results);
        } finally {
            foreach ($workers as $worker) {
                if ($worker->isRunning()) {
                    $worker->stop();
                }
            }
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }
}
