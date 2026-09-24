<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
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
    public function view(User $user, Application $application): bool
    {
        if ($user->roles()->count() > 0) {
            return true;
        }

        if ($user->id === $application->client->user_id) {
            return true;
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
    public function update(User $user, Application $application): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Application $application): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->id === $application->client->user_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Application $application): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->id === $application->client->user_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Application $application): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return false;
    }

    public function cancel(User $user, Application $application): bool
    {
        if ($application->cancellation) {
            return false;
        }

        if ($application->referral) {
            return false;
        }

        if ($application->rejection) {
            return false;
        }

        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->id === $application->client->user_id;
    }

    public function reject(User $user, Application $application): bool
    {
        if ($application->cancellation) {
            return false;
        }

        if ($application->referral) {
            return false;
        }

        if ($application->rejection) {
            return false;
        }

        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->hasPermissionTo('rejection.create');
    }

    public function refer(User $user, Application $application): bool
    {
        if ($application->cancellation) {
            return false;
        }

        if ($application->referral) {
            return false;
        }

        if ($application->rejection) {
            return false;
        }

        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->hasPermissionTo('referral.create');
    }
}
