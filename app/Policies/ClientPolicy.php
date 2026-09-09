<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use App\Traits\HasSuperadminByPass;

class ClientPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->roles()->count() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Client $client): bool
    {
        if ($user->roles()->count() > 0) {
            return true;
        }

        if ($user->roles()->count() == 0) {
            if ($user->id == $client->user_id) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->roles()->count() == 0) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Client $client): bool
    {
        if ($client->application) {
            return false;
        }
        
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->id === $client->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Client $client): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->id === $client->user_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Client $client): bool
    {
        if ($user->id === $client->user_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Client $client): bool
    {
        return false;
    }
}
