<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\User;
use App\Services\WorkflowHistoryService;
use Illuminate\Database\Seeder;

class WorkflowHistorySeeder extends Seeder
{
    public function run(): void
    {
        $previous = auth()->user();
        auth()->setUser(User::role('superadmin')->firstOrFail());
        try {
            foreach (Application::with(['activeRecommendation.workflow', 'workflowStage'])->whereNotNull('current_recommendation_uuid')->get() as $application) {
                $version = $application->currentWorkflow();
                if (! $version || ! $version->is_active || $application->closure()->exists() ||
                    $application->rejection()->exists() || $application->cancellation()->exists() || $application->referral()->exists()) {
                    continue;
                }
                while ($target = $application->toStage()) {
                    $run = $application->currentRecommendation();
                    app(WorkflowHistoryService::class)->store([
                        'recommendation_uuid' => $run->uuid,
                        'expected_stage_uuid' => $application->current_workflow_stage_uuid,
                        'to_stage_uuid' => $target->uuid, 'date_in' => now(), 'date_out' => now()->addMinutes(5),
                    ]);
                    $application->refresh();
                    if (random_int(0, 2) === 0) {
                        break;
                    }
                }
            }
        } finally {
            $previous ? auth()->setUser($previous) : auth()->forgetUser();
        }
    }
}
