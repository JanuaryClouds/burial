<?php

namespace App\Traits;

use App\Models\Application;
use App\Models\WorkflowHistory;
use App\Models\WorkflowStage;

trait HasWorkflowHistory
{
    protected function currentStage(Application $application): ?WorkflowStage
    {
        return $application->currentStage();
    }

    protected function previousStage(Application $application): ?WorkflowStage
    {
        $current = $application->currentStage();

        return $application->currentWorkflow()?->stages()
            ->where('position', ($current ? $current->position : 0) - 1)->first();
    }

    protected function nextStage(Application $application): ?WorkflowStage
    {
        return $application->toStage();
    }

    protected function previousStageHistory(Application $application): ?WorkflowHistory
    {
        return $application->currentRecommendation()?->workflowHistory()
            ->where('to_stage_uuid', $application->current_workflow_stage_uuid)->latest()->first();
    }

    protected function nextStageHistory(Application $application): ?WorkflowHistory
    {
        return $application->currentRecommendation()?->workflowHistory()
            ->where('from_stage_uuid', $application->current_workflow_stage_uuid)->oldest()->first();
    }
}
