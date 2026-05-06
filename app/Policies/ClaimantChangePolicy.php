<?php

namespace App\Policies;

use App\Models\BurialAssistance;
use App\Models\ClaimantChange;
use App\Models\User;

class ClaimantChangePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ClaimantChange $claimantChange): bool
    {
        if ($user->id == $claimantChange->originalClient()?->client?->user_id) {
            return true;
        }

        if ($user->can('edit-claimant-changes')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, BurialAssistance $burialAssistance): bool
    {
        if ($user->roles()->exists() && $user->can('create-claimant-changes')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ClaimantChange $claimantChange): bool
    {
        return $user->can('edit-claimant-changes');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ClaimantChange $claimantChange): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ClaimantChange $claimantChange): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ClaimantChange $claimantChange): bool
    {
        return false;
    }
}
