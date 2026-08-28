<?php

namespace App\Traits;

use App\Models\Application;
use App\Models\WorkflowHistory;
use App\Models\WorkflowStage;

trait HasWorkflowHistory
{
    protected function currentStage(Application $application): ?WorkflowStage
    {
        return WorkflowStage::firstWhere('uuid', $application->current_workflow_stage_uuid);
    }

    protected function previousStage(Application $application): ?WorkflowStage
    {
        return WorkflowStage::firstWhere('position', $this->currentStage($application)->position - 1);
    }

    protected function nextStage(Application $application): ?WorkflowStage
    {
        return WorkflowStage::firstWhere('position', $this->currentStage($application)->position + 1);
    }

    protected function previousStageHistory(Application $application): ?WorkflowHistory
    {
        return WorkflowHistory::where('to_stage_uuid', $this->currentStage($application)->uuid)
            ->latest()
            ->first();
    }

    protected function nextStageHistory(Application $application): ?WorkflowHistory
    {
        return WorkflowHistory::where('from_stage_uuid', $this->currentStage($application)->uuid)
            ->oldest()
            ->first();
    }
}
