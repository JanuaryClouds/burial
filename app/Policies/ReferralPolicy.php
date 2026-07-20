<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\Client;
use App\Models\Referral;
use App\Models\User;

class ReferralPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Application $application): bool
    {
        if ($user->id === $application->client->user_id) {
            return false;
        }

        return $user->can('create-referrals');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Referral $referral): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Referral $referral): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Referral $referral): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Referral $referral): bool
    {
        return false;
    }
}
