<?php

namespace App\Policies;

use App\Models\FuneralAssistance;
use App\Models\User;

class FuneralAssistancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->roles()->count() > 0) {
            return $user->can('view-libreng-libings');
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FuneralAssistance $funeralAssistance): bool
    {
        if ($user->roles()->count() > 0) {
            return $user->can('view-libreng-libings');
        }

        return $user->id == $funeralAssistance->client->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create-libreng-libings');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FuneralAssistance $funeralAssistance): bool
    {
        return $user->can('update-libreng-libing');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FuneralAssistance $funeralAssistance): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FuneralAssistance $funeralAssistance): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FuneralAssistance $funeralAssistance): bool
    {
        //
    }
}
