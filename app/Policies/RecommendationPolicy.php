<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use App\Models\WorkflowStage;
use App\Traits\HasSuperadminByPass;

class RecommendationPolicy
{
    public function create(User $user, Application $application): bool
    {
        if ($user->id === $application->client->user_id) {
            return false;
        }

        if (! $application->assessment) {
            return false;
        }

        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('recommendation.create');
    }
}
