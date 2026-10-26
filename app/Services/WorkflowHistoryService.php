<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Recommendation;
use App\Models\WorkflowHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WorkflowHistoryService
{
    public function store(array $data): WorkflowHistory
    {
        Validator::make($data, [
            'recommendation_uuid' => ['required', 'uuid', 'exists:recommendations,uuid'],
            'expected_stage_uuid' => ['present', 'nullable', 'uuid'],
            'to_stage_uuid' => ['required', 'uuid'],
            'date_in' => ['required', 'date'],
            'date_out' => ['required', 'date', 'after_or_equal:date_in'],
            'reason' => ['nullable', 'string', 'max:65535'],
            'remark' => ['nullable', 'string', 'max:65535'],
            'custom_values' => ['sometimes', 'array'],
        ])->validate();

        $run = Recommendation::with('workflow')->whereKey($data['recommendation_uuid'])->firstOrFail();

        return DB::transaction(function () use ($data, $run) {
            $version = $run->workflow;
            if (! $version) {
                throw ValidationException::withMessages(['toStageUuid' => 'This legacy recommendation has no workflow. Create a new recommendation.']);
            }
            app(WorkflowVersionService::class)->lockGroup($version);
            $version = $version->newQuery()->whereKey($version->uuid)->lockForUpdate()->firstOrFail();
            $application = Application::whereKey($run->application_uuid)->lockForUpdate()->firstOrFail();
            if ($application->current_claimant_assignment_uuid !== $run->claimant_assignment_uuid ||
                $application->current_recommendation_uuid !== $run->uuid ||
                $application->current_workflow_stage_uuid !== $data['expected_stage_uuid']) {
                throw ValidationException::withMessages(['toStageUuid' => 'Processing changed after this form was opened. Reload the current run.']);
            }
            abort_if($application->isFormerClaimant(auth()->id()), 403);
            abort_unless($application->finishedInterview() && $application->assessment()->exists(), 403);
            if (! $version->is_active) {
                throw ValidationException::withMessages(['toStageUuid' => 'This workflow version is paused. Wait for reactivation or create a new recommendation.']);
            }
            abort_if($application->closure()->lockForUpdate()->first() !== null || $application->rejection()->lockForUpdate()->first() !== null ||
                $application->cancellation()->lockForUpdate()->first() !== null || $application->referral()->lockForUpdate()->first() !== null, 403);
            $target = $version->stages()->with(['permission', 'customFields'])->whereKey($data['to_stage_uuid'])->first();
            if (! $target) {
                throw ValidationException::withMessages(['toStageUuid' => 'Choose a stage from the assigned workflow version.']);
            }
            $current = $application->workflowStage;
            if ($current && ($current->workflow_uuid !== $version->uuid || $current->trashed() || ! $current->position)) {
                throw ValidationException::withMessages(['toStageUuid' => 'The current stage is inconsistent. Contact a superadmin.']);
            }
            $position = $current ? $current->position : 0;
            if ($target->position !== $position + 1 && ! ($position > 0 && $target->position < $position)) {
                throw ValidationException::withMessages(['toStageUuid' => 'Only the next stage or an earlier stage is allowed.']);
            }
            if ($target->position < $position && blank($data['reason'] ?? null)) {
                throw ValidationException::withMessages(['reason' => 'A reason is required when returning to an earlier stage.']);
            }
            abort_unless(auth()->user() && (auth()->user()->hasRole('superadmin') ||
                ($target->permission && auth()->user()->can($target->permission->name))), 403);
            $values = app(WorkflowCustomFieldService::class)->validateValues($target, $data['custom_values'] ?? []);
            $history = WorkflowHistory::create([
                'recommendation_uuid' => $run->uuid, 'from_stage_uuid' => $current?->uuid,
                'to_stage_uuid' => $target->uuid, 'date_in' => $data['date_in'], 'date_out' => $data['date_out'],
                'reason' => $data['reason'] ?? null, 'processed_by' => auth()->id(),
            ]);
            foreach ($values as $fieldUuid => $value) {
                $history->customValues()->create(['custom_field_uuid' => $fieldUuid, 'value' => $value]);
            }
            if (filled($data['remark'] ?? null)) {
                app(RemarkService::class)->store(['content' => $data['remark']], $history);
            }
            $application->update(['current_workflow_stage_uuid' => $target->uuid]);
            ActivityLoggerService::logSuccess('Logged workflow stage', [
                'application_uuid' => $application->uuid, 'recommendation_uuid' => $run->uuid,
                'workflow_uuid' => $version->uuid, 'workflow_history_uuid' => $history->uuid,
            ]);

            return $history;
        }, 3);
    }
}
