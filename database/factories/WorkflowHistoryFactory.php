<?php

namespace Database\Factories;

use App\Models\Recommendation;
use App\Models\User;
use App\Models\WorkflowHistory;
use App\Services\WorkflowHistoryService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/** @extends Factory<WorkflowHistory> */
class WorkflowHistoryFactory extends Factory
{
    public function definition(): array
    {
        return ['date_in' => now(), 'date_out' => now()->addMinutes(5)];
    }

    public function create($attributes = [], ?Model $parent = null)
    {
        $models = $this->make($attributes, $parent);
        $previous = auth()->user();
        try {
            $store = function (WorkflowHistory $model) {
                auth()->setUser($model->processed_by ? User::findOrFail($model->processed_by) :
                    (auth()->user() ?? User::role('superadmin')->firstOrFail()));
                $run = Recommendation::with(['application.activeRecommendation.workflow', 'application.workflowStage'])->findOrFail($model->recommendation_uuid);

                return app(WorkflowHistoryService::class)->store([
                    'recommendation_uuid' => $run->uuid,
                    'expected_stage_uuid' => $run->application->current_workflow_stage_uuid,
                    'to_stage_uuid' => $model->to_stage_uuid ?? $run->application->toStage()?->uuid,
                    'date_in' => $model->date_in, 'date_out' => $model->date_out, 'reason' => $model->reason,
                ]);
            };
            $results = $models instanceof WorkflowHistory ? $store($models) : new Collection($models->map($store)->all());
            $this->callAfterCreating($results instanceof WorkflowHistory ? new Collection([$results]) : $results, $parent);

            return $results;
        } finally {
            $previous ? auth()->setUser($previous) : auth()->forgetUser();
        }
    }
}
