<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Recommendation;
use App\Models\Workflow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RecommendationService
{
    public function store(array $data, string $applicationUuid): Recommendation
    {
        Validator::make($data, [
            'workflow_uuid' => ['required', 'uuid', 'exists:workflows,uuid'],
            'funeral_assistance_type_uuid' => ['required', 'uuid', 'exists:funeral_assistance_types,uuid,deleted_at,NULL'],
            'amount_extended' => ['required', 'numeric', 'min:0'],
            'mode_of_assistance_id' => ['required', 'exists:mode_of_assistances,id'],
            'expected_assignment_uuid' => ['required', 'uuid'],
            'expected_recommendation_uuid' => ['present', 'nullable', 'uuid'],
        ])->validate();

        $hint = Workflow::whereKey($data['workflow_uuid'])->firstOrFail();

        return DB::transaction(function () use ($data, $applicationUuid, $hint) {
            $version = $hint;
            app(WorkflowVersionService::class)->lockGroup($version);
            $version = Workflow::whereKey($version->uuid)->lockForUpdate()->firstOrFail();
            if (! $version->isSelectable()) {
                throw ValidationException::withMessages(['form.workflowUuid' => 'This version is no longer available. Select the latest active workflow.']);
            }
            $application = Application::whereKey($applicationUuid)->lockForUpdate()->firstOrFail();
            $application->assertAssignment($data['expected_assignment_uuid']);
            abort_if($application->isFormerClaimant(auth()->id()), 403);
            $application->load(['client.interviews', 'assessment', 'rejection', 'cancellation', 'referral', 'closure', 'workflowStage']);
            Gate::authorize('create', [Recommendation::class, $application]);
            if ($application->current_recommendation_uuid !== $data['expected_recommendation_uuid']) {
                throw ValidationException::withMessages(['form.workflowUuid' => 'The application has a newer recommendation. Reload before restarting.']);
            }
            $recommendation = Recommendation::create([
                'application_uuid' => $application->uuid,
                'claimant_assignment_uuid' => $application->current_claimant_assignment_uuid,
                'workflow_uuid' => $version->uuid,
                'funeral_assistance_type_uuid' => $data['funeral_assistance_type_uuid'],
                'amount_extended' => $data['amount_extended'],
                'mode_of_assistance_id' => $data['mode_of_assistance_id'],
                'recommended_by' => auth()->id(),
            ]);
            $application->update(['current_recommendation_uuid' => $recommendation->uuid, 'current_workflow_stage_uuid' => null]);
            ActivityLoggerService::logSuccess('Started recommendation workflow run', [
                'application_uuid' => $application->uuid, 'recommendation_uuid' => $recommendation->uuid,
                'workflow_uuid' => $version->uuid, 'previous_recommendation_uuid' => $data['expected_recommendation_uuid'],
            ]);

            return $recommendation;
        }, 3);
    }
}
