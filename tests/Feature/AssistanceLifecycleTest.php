<?php

namespace Tests\Feature;

use App\Livewire\Cancellation\Create;
use App\Models\Application;
use App\Models\Beneficiary;
use App\Models\Cancellation;
use App\Models\Client;
use App\Models\FuneralAssistanceType;
use App\Models\Referral;
use App\Models\Rejection;
use App\Models\User;
use App\Services\ApplicationProcessingService;
use App\Services\ApplicationService;
use App\Services\AssessmentService;
use App\Services\BeneficiaryService;
use App\Services\ChequeService;
use App\Services\ClientService;
use App\Services\InterviewService;
use App\Services\PsaClassificationService;
use App\Services\RecommendationService;
use App\Services\RemarkService;
use App\Services\WorkflowHistoryService;
use App\Services\WorkflowVersionService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AssistanceLifecycleTest extends TestCase
{
    private User $owner;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'lifecycle_testing', 'database.connections.lifecycle_testing' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true,
        ], 'cache.default' => 'array', 'session.driver' => 'array']);
        foreach (['genders', 'relationships', 'civilStatus', 'religions', 'nationalities', 'educations', 'districts', 'assistances', 'modes'] as $lookup) {
            Cache::put($lookup, collect(), 3600);
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Artisan::call('migrate', ['--database' => 'lifecycle_testing', '--force' => true]);
        foreach (['sexes', 'religions', 'nationalities', 'civil_statuses', 'educations', 'relationships', 'mode_of_assistances'] as $table) {
            DB::table($table)->insert(['name' => $table]);
        }
        foreach (['Create Interview', 'Create Assessment', 'Create Recommendation', 'Create Rejection', 'Create Referral', 'Create Closure'] as $permission) {
            Permission::findOrCreate($permission, 'web');
        }
        $this->owner = User::factory()->create(['password' => 'Testing-password-123!']);
        $this->staff = User::factory()->superadmin()->create(['password' => 'Testing-password-123!']);
        $this->actingAs($this->owner);
    }

    protected function tearDown(): void
    {
        DB::purge('lifecycle_testing');
        parent::tearDown();
    }

    private function drafts(): array
    {
        $client = app(ClientService::class)->store(['dateOfBirth' => '1980-01-01', 'contactNumber' => '09123456789']);
        $beneficiary = app(BeneficiaryService::class)->store([
            'firstName' => 'Recorded', 'middleName' => null, 'lastName' => 'Beneficiary', 'suffix' => null,
            'sexId' => 1, 'religionId' => 1, 'dateOfBirth' => '1950-01-01', 'dateOfDeath' => '2026-01-01', 'pwd' => true,
        ]);

        return [$client, $beneficiary];
    }

    private function submit(): Application
    {
        [$client, $beneficiary] = $this->drafts();

        return app(ApplicationService::class)->store($client, $beneficiary, 1);
    }

    private function completeInterview(Application $application): void
    {
        $this->actingAs($this->staff);
        $interview = app(InterviewService::class)->store(['schedule' => now()->addDay()->toDateTimeString()], $application->client_uuid, $application->current_claimant_assignment_uuid);
        app(InterviewService::class)->done($interview->uuid);
        $this->assertSame('done', $interview->fresh()->status);
    }

    private function assess(Application $application): void
    {
        app(AssessmentService::class)->store(['expected_assignment_uuid' => $application->current_claimant_assignment_uuid,
            'problem_presented' => 'Unable to afford funeral costs', 'swa' => 'Eligible for assistance'], $application->uuid);
    }

    private function recommendationData(Application $application): array
    {
        $permission = Permission::findOrCreate('Workflow Verification', 'web');
        $workflow = app(WorkflowVersionService::class)->publish(['name' => 'Assistance', 'description' => 'Processing', 'stages' => [
            ['name' => 'Verification', 'description' => 'Verify documents', 'permission_id' => (string) $permission->id],
            ['name' => 'Approval', 'description' => 'Approve assistance', 'permission_id' => (string) $permission->id],
        ]]);
        $type = FuneralAssistanceType::factory()->create(['name' => 'Burial', 'description' => 'Funeral assistance', 'workflow_uuid' => $workflow->uuid]);

        return ['workflow_uuid' => $workflow->uuid, 'funeral_assistance_type_uuid' => $type->uuid, 'amount_extended' => 2500,
            'mode_of_assistance_id' => 1, 'expected_assignment_uuid' => $application->current_claimant_assignment_uuid, 'expected_recommendation_uuid' => null];
    }

    private function denied(callable $operation): void
    {
        try {
            $operation();
            $this->fail('Expected authorization or validation denial.');
        } catch (AuthorizationException $exception) {
            $this->assertSame(403, $exception->status() ?? 403);
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        } catch (ValidationException $exception) {
            $this->assertNotEmpty($exception->errors());
        }
    }

    public function test_drafts_and_submission_preserve_ownership_and_initial_assignment(): void
    {
        $application = $this->submit();
        $this->assertDatabaseHas('clients', ['uuid' => $application->client_uuid, 'user_id' => $this->owner->id, 'contact_number' => '09123456789']);
        $this->assertDatabaseHas('beneficiaries', ['uuid' => $application->beneficiary_uuid, 'created_by' => $this->owner->id, 'pwd' => 1]);
        $this->assertNotEmpty($application->tracking_no);
        $this->assertDatabaseHas('application_claimant_assignments', ['uuid' => $application->current_claimant_assignment_uuid, 'application_uuid' => $application->uuid, 'client_uuid' => $application->client_uuid]);
        $this->assertTrue($application->isActive());
        $this->denied(fn () => app(ApplicationService::class)->store($application->client, $application->beneficiary, 1));
        $this->assertDatabaseCount('applications', 1);
    }

    public function test_submission_rejects_each_foreign_draft_without_partial_application(): void
    {
        [$client, $beneficiary] = $this->drafts();
        $this->actingAs(User::factory()->create());
        [$otherClient, $otherBeneficiary] = $this->drafts();
        $this->denied(fn () => app(ApplicationService::class)->store($client, $otherBeneficiary, 1));
        $this->denied(fn () => app(ApplicationService::class)->store($otherClient, $beneficiary, 1));
        $this->assertDatabaseCount('applications', 0);
        $this->assertDatabaseCount('application_claimant_assignments', 0);
    }

    public function test_foreign_and_submitted_drafts_cannot_be_edited(): void
    {
        $application = $this->submit();
        $this->denied(fn () => app(ClientService::class)->update($application->client, ['dateOfBirth' => '1990-01-01', 'contactNumber' => '09999999999']));
        $this->actingAs(User::factory()->create());
        $this->denied(fn () => app(ClientService::class)->update($application->client, ['dateOfBirth' => '1990-01-01', 'contactNumber' => '09999999999']));
        $this->assertDatabaseHas('clients', ['uuid' => $application->client_uuid, 'contact_number' => '09123456789']);
    }

    public function test_interview_and_assessment_require_staff_order_and_unique_processing(): void
    {
        $application = $this->submit();
        $this->denied(fn () => app(InterviewService::class)->store(['schedule' => now()->addDay()], $application->client_uuid, $application->current_claimant_assignment_uuid));
        $this->actingAs($this->staff);
        $this->denied(fn () => $this->assess($application));
        $this->denied(fn () => app(InterviewService::class)->store(['schedule' => '2000-01-01'], $application->client_uuid, $application->current_claimant_assignment_uuid));
        $this->assertDatabaseCount('interviews', 0);
        $this->completeInterview($application);
        $this->denied(fn () => app(InterviewService::class)->store(['schedule' => now()->addDays(2)], $application->client_uuid, $application->current_claimant_assignment_uuid));
        $this->assess($application);
        $this->denied(fn () => $this->assess($application));
        $this->assertDatabaseCount('assessments', 1);
        $this->assertDatabaseHas('assessments', ['application_uuid' => $application->uuid, 'claimant_assignment_uuid' => $application->current_claimant_assignment_uuid]);
    }

    public function test_recommendation_stage_history_and_audit_are_persisted_atomically(): void
    {
        $application = $this->submit();
        $this->completeInterview($application);
        $data = $this->recommendationData($application);
        $this->denied(fn () => app(RecommendationService::class)->store($data, $application->uuid));
        $this->assess($application);
        $run = app(RecommendationService::class)->store($data, $application->uuid);
        $this->assertSame($run->uuid, $application->fresh()->current_recommendation_uuid);
        $this->denied(fn () => app(RecommendationService::class)->store($data, $application->uuid));
        $stages = $run->workflow->stages()->orderBy('position')->get();
        $historyData = ['recommendation_uuid' => $run->uuid, 'expected_stage_uuid' => null, 'to_stage_uuid' => $stages[0]->uuid,
            'date_in' => '2026-11-01', 'date_out' => '2026-11-02', 'remark' => 'Documents checked'];
        $this->denied(fn () => app(WorkflowHistoryService::class)->store([...$historyData, 'to_stage_uuid' => $stages[1]->uuid]));
        $history = app(WorkflowHistoryService::class)->store($historyData);
        $this->assertSame($stages[0]->uuid, $application->fresh()->current_workflow_stage_uuid);
        $this->assertSame($this->staff->id, $history->processed_by);
        $this->assertSame('Documents checked', $history->remarks()->first()->content);
        $this->denied(fn () => app(WorkflowHistoryService::class)->store($historyData));
        $this->assertDatabaseCount('workflow_histories', 1);
        $audit = Activity::where('description', 'Logged workflow stage')->sole();
        $this->assertEquals($this->staff->id, $audit->causer_id);
        $this->assertSame($history->uuid, $audit->properties['details']['workflow_history_uuid']);
    }

    public static function outcomes(): array
    {
        return [['cancel', 'cancellations', 'cancelled'], ['reject', 'rejections', 'rejected'], ['refer', 'referrals', 'referred']];
    }

    #[DataProvider('outcomes')]
    public function test_outcomes_end_processing_and_can_be_closed_once(string $action, string $table, string $status): void
    {
        $application = $this->submit();
        $this->completeInterview($application);
        $this->assess($application);
        $run = app(RecommendationService::class)->store($this->recommendationData($application), $application->uuid);
        $service = app(ApplicationProcessingService::class);
        $this->denied(fn () => $service->outcome($application->uuid, $application->current_claimant_assignment_uuid, 'close', ['reason' => 'Premature']));
        $service->outcome($application->uuid, $application->current_claimant_assignment_uuid, $action, ['reason' => 'Recorded outcome', 'referred_to' => 'Partner agency']);
        $this->assertDatabaseHas($table, ['application_uuid' => $application->uuid]);
        $model = ['cancel' => Cancellation::class, 'reject' => Rejection::class, 'refer' => Referral::class][$action];
        $this->assertSame('Recorded outcome', $model::sole()->reason);
        $this->assertNotSame('Recorded outcome', DB::table($table)->value('reason'));
        $this->assertSame($status, $run->fresh()->status);
        $this->assertFalse($application->fresh()->isActive());
        $this->denied(fn () => $service->outcome($application->uuid, $application->current_claimant_assignment_uuid, $action, ['reason' => 'Duplicate', 'referred_to' => 'Other']));
        $this->denied(fn () => $this->assess($application));
        $service->outcome($application->uuid, $application->current_claimant_assignment_uuid, 'close', ['reason' => 'Finalized']);
        $this->assertDatabaseHas('closures', ['application_uuid' => $application->uuid, 'closed_by' => $this->staff->id]);
        $this->denied(fn () => $service->outcome($application->uuid, $application->current_claimant_assignment_uuid, 'close', ['reason' => 'Duplicate']));
        $this->assertDatabaseCount('closures', 1);
        $this->assertDatabaseCount($table, 1);
    }

    #[DataProvider('outcomes')]
    public function test_unrelated_citizen_cannot_record_outcomes(string $action, string $table, string $status): void
    {
        $application = $this->submit();
        $this->actingAs(User::factory()->create());
        $this->denied(fn () => app(ApplicationProcessingService::class)->outcome($application->uuid, $application->current_claimant_assignment_uuid, $action, ['reason' => 'Unauthorized', 'referred_to' => 'Agency']));
        $this->assertDatabaseCount($table, 0);
        $this->assertSame(0, Activity::where('description', 'Application outcome: '.$action)->count());
    }

    public function test_inactive_account_cannot_create_records_or_cancel_existing_application(): void
    {
        $application = $this->submit();
        $this->owner->update(['is_active' => false]);
        $this->denied(fn () => $this->drafts());
        $this->denied(fn () => app(ApplicationProcessingService::class)->outcome($application->uuid, $application->current_claimant_assignment_uuid, 'cancel', ['reason' => 'Inactive']));
        $this->assertDatabaseCount('clients', 1);
        $this->assertDatabaseCount('cancellations', 0);
    }

    public function test_owner_can_cancel_but_cannot_perform_staff_outcomes(): void
    {
        $application = $this->submit();
        $service = app(ApplicationProcessingService::class);
        foreach (['reject', 'refer', 'close'] as $action) {
            $this->denied(fn () => $service->outcome($application->uuid, $application->current_claimant_assignment_uuid, $action, ['reason' => 'Unauthorized', 'referred_to' => 'Agency']));
        }
        $service->outcome($application->uuid, $application->current_claimant_assignment_uuid, 'cancel', ['reason' => 'Withdrawn by applicant']);
        $this->assertDatabaseHas('cancellations', ['application_uuid' => $application->uuid, 'cancelled_by' => $this->owner->id]);
    }

    public static function outcomeComponents(): array
    {
        return [
            [Create::class, 'cancellations'],
            [\App\Livewire\Rejection\Create::class, 'rejections'],
            [\App\Livewire\Referral\Create::class, 'referrals'],
            [\App\Livewire\Closure\Create::class, 'closures'],
        ];
    }

    #[DataProvider('outcomeComponents')]
    public function test_outcome_forms_reject_missing_reason(string $component, string $table): void
    {
        $application = $this->submit();
        $this->actingAs($this->staff);
        Livewire::test($component, ['application' => $application])->call('save')->assertDispatched('notification:toast');
        $this->assertDatabaseCount($table, 0);
    }

    public function test_cancellation_form_saves_reason_and_redirects(): void
    {
        $application = $this->submit();
        Livewire::test(Create::class, ['application' => $application])
            ->set('form.reason', 'Applicant withdrew request')->call('save')->assertHasNoErrors()
            ->assertRedirect(route('application.show', $application));
        $this->assertSame('Applicant withdrew request', Cancellation::sole()->reason);
    }

    public function test_stale_assignment_rejects_writes_without_outcome_or_audit(): void
    {
        $application = $this->submit();
        $this->actingAs($this->staff);
        $this->denied(fn () => app(ApplicationProcessingService::class)->outcome($application->uuid, (string) Str::uuid(), 'reject', ['reason' => 'Stale form']));
        $this->denied(fn () => app(InterviewService::class)->store(['schedule' => now()->addDay()], $application->client_uuid, (string) Str::uuid()));
        $this->assertDatabaseCount('rejections', 0);
        $this->assertDatabaseCount('interviews', 0);
        $this->assertSame(0, Activity::where('description', 'Application outcome: reject')->count());
    }

    public function test_assessment_requires_both_text_fields(): void
    {
        $application = $this->submit();
        $this->completeInterview($application);
        foreach (['problem_presented', 'swa'] as $field) {
            try {
                app(AssessmentService::class)->store(['expected_assignment_uuid' => $application->current_claimant_assignment_uuid,
                    'problem_presented' => 'Costs', 'swa' => 'Needs assistance', $field => ''], $application->uuid);
                $this->fail('Expected required field validation.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey($field, $exception->errors());
            }
        }
        $this->assertDatabaseCount('assessments', 0);
    }

    public function test_recommendation_rejects_negative_amount_and_missing_run_token(): void
    {
        $application = $this->submit();
        $this->completeInterview($application);
        $this->assess($application);
        $data = $this->recommendationData($application);
        foreach (['amount_extended', 'expected_recommendation_uuid'] as $field) {
            $invalid = $data;
            if ($field === 'amount_extended') {
                $invalid[$field] = -1;
            } else {
                unset($invalid[$field]);
            }
            try {
                app(RecommendationService::class)->store($invalid, $application->uuid);
                $this->fail('Expected recommendation validation.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey($field, $exception->errors());
            }
        }
        $this->assertDatabaseCount('recommendations', 0);
        $this->assertNull($application->fresh()->current_recommendation_uuid);
    }

    public function test_workflow_history_rolls_back_when_remark_storage_fails(): void
    {
        $application = $this->submit();
        $this->completeInterview($application);
        $this->assess($application);
        $run = app(RecommendationService::class)->store($this->recommendationData($application), $application->uuid);
        $this->mock(RemarkService::class, function ($mock) {
            $mock->shouldReceive('store')->once()->andThrow(new \RuntimeException('Simulated remark failure'));
        });
        try {
            app(WorkflowHistoryService::class)->store(['recommendation_uuid' => $run->uuid, 'expected_stage_uuid' => null,
                'to_stage_uuid' => $run->workflow->stages()->orderBy('position')->first()->uuid,
                'date_in' => '2026-11-01', 'date_out' => '2026-11-02', 'remark' => 'Documents checked']);
            $this->fail('Expected simulated storage failure.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Simulated remark failure', $exception->getMessage());
        }
        $this->assertDatabaseCount('workflow_histories', 0);
        $this->assertNull($application->fresh()->current_workflow_stage_uuid);
        $this->assertSame(0, Activity::where('description', 'Logged workflow stage')->count());
    }

    private function mockGeography(): void
    {
        $this->partialMock(PsaClassificationService::class, function ($mock) {
            $mock->shouldReceive('getRegionOptions')->andReturn(['13:0:0:0' => 'NCR']);
            $mock->shouldReceive('getProvinceOptions')->andReturn([]);
            $mock->shouldReceive('getMunicipalityOptions')->andReturn(['13:0:137607:0' => 'Taguig']);
            $mock->shouldReceive('getBarangayOptions')->andReturn(['13:0:137607:137607001' => 'Bagumbayan']);
        });
    }

    public function test_client_creation_form_persists_complete_draft(): void
    {
        $this->mockGeography();
        Livewire::test(\App\Livewire\Client\Create::class)
            ->set('form', ['dateOfBirth' => '1980-01-01', 'contactNumber' => '09123456789'])
            ->set('addressForm.regionCode', '13:0:0:0')
            ->set('addressForm.municipalityCode', '13:0:137607:0')
            ->set('addressForm.barangayCode', '13:0:137607:137607001')
            ->set('addressForm.street', 'Main Street')->set('addressForm.houseNumber', '1')
            ->set('demographicsForm', ['sexId' => 1, 'nationalityId' => 1, 'religionId' => 1])
            ->set('socialInfoForm', ['civilId' => 1, 'educationId' => 1, 'income' => '5000'])
            ->call('save')->assertRedirect(route('beneficiary.create'));
        $client = Client::sole();
        $this->assertEquals($this->owner->id, $client->user_id);
        $this->assertSame($client->uuid, session('client_uuid'));
        $this->assertSame('Main Street', $client->address->street);
        $this->assertEquals(1, $client->demographic->sex_id);
        $this->assertEquals(1, $client->socialInfo->education_id);
    }

    public function test_beneficiary_creation_form_persists_address_and_family(): void
    {
        $this->mockGeography();
        Livewire::test(\App\Livewire\Beneficiary\Create::class)
            ->set('form', ['firstName' => 'Recorded', 'lastName' => 'Beneficiary', 'dateOfBirth' => '1950-01-01',
                'dateOfDeath' => '2026-01-01', 'sexId' => 1, 'religionId' => 1, 'family' => [
                    ['name' => 'Family Member', 'age' => 40, 'civilId' => '1', 'sexId' => 1, 'relationshipId' => 1, 'occupation' => 'Worker', 'income' => '5000'],
                ]])
            ->set('addressForm.regionCode', '13:0:0:0')
            ->set('addressForm.municipalityCode', '13:0:137607:0')
            ->set('addressForm.barangayCode', '13:0:137607:137607001')
            ->set('addressForm.street', 'Main Street')->set('addressForm.houseNumber', '1')->call('save')->assertRedirect(route('application.create'));
        $beneficiary = Beneficiary::sole();
        $this->assertEquals($this->owner->id, $beneficiary->created_by);
        $this->assertSame($beneficiary->uuid, session('beneficiary_uuid'));
        $this->assertSame('Main Street', $beneficiary->address->street);
        $this->assertSame('Family Member', $beneficiary->family()->sole()->name);
    }

    public static function draftComponents(): array
    {
        return [[\App\Livewire\Client\Create::class, 'clients'], [\App\Livewire\Beneficiary\Create::class, 'beneficiaries']];
    }

    #[DataProvider('draftComponents')]
    public function test_empty_draft_form_does_not_create_partial_records(string $component, string $table): void
    {
        $this->mockGeography();
        Livewire::test($component)->call('save')->assertDispatched('notification:toast');
        $this->assertDatabaseCount($table, 0);
        $this->assertDatabaseCount('addresses', 0);
    }

    #[DataProvider('draftComponents')]
    public function test_staff_cannot_open_citizen_creation_forms(string $component, string $table): void
    {
        $this->actingAs($this->staff);
        Livewire::test($component)->assertForbidden();
        $this->assertDatabaseCount($table, 0);
    }

    #[DataProvider('outcomes')]
    public function test_office_outcome_requires_specific_permission(string $action, string $table, string $status): void
    {
        $application = $this->submit();
        $officer = User::factory()->create();
        $officer->assignRole(Role::findOrCreate('CSWDO', 'web'));
        $this->actingAs($officer);
        $service = app(ApplicationProcessingService::class);
        if ($action === 'cancel') {
            $this->denied(fn () => $service->outcome($application->uuid, $application->current_claimant_assignment_uuid, $action, ['reason' => 'Not owner']));
            $this->assertDatabaseCount($table, 0);

            return;
        }
        $this->denied(fn () => $service->outcome($application->uuid, $application->current_claimant_assignment_uuid, $action, ['reason' => 'No permission', 'referred_to' => 'Agency']));
        $this->assertDatabaseCount($table, 0);
        $officer->givePermissionTo($action === 'reject' ? 'Create Rejection' : 'Create Referral');
        $this->actingAs($officer->fresh());
        $service->outcome($application->uuid, $application->current_claimant_assignment_uuid, $action, ['reason' => 'Authorized office outcome', 'referred_to' => 'Agency']);
        $this->assertDatabaseCount($table, 1);
    }

    public function test_cheque_issue_claim_and_immutability(): void
    {
        $application = $this->submit();
        $this->actingAs($this->staff);
        $service = app(ChequeService::class);
        $data = ['obr_number' => 'OBR-2026-001', 'cheque_number' => 'CHK-001', 'amount' => 2500, 'status' => 'issued'];
        $cheque = $service->save($application->uuid, $application->current_claimant_assignment_uuid, $data);
        $this->assertTrue($application->hasPaymentBlock());
        $this->denied(fn () => $service->save($application->uuid, $application->current_claimant_assignment_uuid, [...$data, 'status' => 'claimed'], $cheque->uuid));
        $service->save($application->uuid, $application->current_claimant_assignment_uuid, [...$data, 'status' => 'claimed', 'date_claimed' => '2026-11-03'], $cheque->uuid);
        $this->denied(fn () => $service->save($application->uuid, $application->current_claimant_assignment_uuid, [...$data, 'status' => 'cancelled'], $cheque->uuid));
        $this->assertSame('claimed', $cheque->fresh()->status);
        $this->assertDatabaseCount('cheques', 1);
    }
}
