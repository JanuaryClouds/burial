<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class RecommendationPolicy
{
    public function create(User $user, Application $application): bool
    {
        if ($user->id === $application->client->user_id) {
            return false;
        }

        return $user->can('create-recommendations');
    }
}
