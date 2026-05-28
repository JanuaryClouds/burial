<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class RecommendationPolicy
{
    public function create(User $user, Client $client): bool
    {
        if ($user->id === $client->user_id) {
            return false;
        }

        return $user->can('create-recommendations');
    }
}
