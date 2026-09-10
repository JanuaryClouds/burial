<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use App\Models\WorkflowStage;
use App\Traits\HasSuperadminByPass;

class RecommendationPolicy
{
    public function view(User $user): bool
    {
        if ($user->roles()->count() > 0) {
            return true;
        }

        return false;
    }

    public function create(User $user, Application $application): bool
    {
        if ($user->id === $application->client->user_id) {
            return false;
        }

        if (! $application->assessment) {
            return false;
        }

        if ($application->rejection) {
            return false;
        }

        if ($application->cancellation) {
            return false;
        }

        if ($application->referral) {
            return false;
        }

        if (!empty(array_intersect(collect($application->status())->pluck('label')->toArray(), ['releasing', 'referred', 'cancelled', 'closed']))) {
            return false;
        }

        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('recommendation.create');
    }
}
