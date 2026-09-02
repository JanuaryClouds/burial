<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use App\Models\WorkflowStage;

class RecommendationPolicy
{
    public function create(User $user, Application $application): bool
    {
        if ($user->id === $application->client->user_id) {
            return false;
        }

        $recommendationStageUuid = WorkflowStage::firstWhere('name', 'recommendation')->uuid;

        if ($application->current_workflow_stage_uuid !== $recommendationStageUuid) {
            return false;
        }

        return $user->can('create-recommendations');
    }
}
