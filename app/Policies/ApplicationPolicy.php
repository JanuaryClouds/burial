<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use App\Services\ApplicationAccessService;

class ApplicationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function changeClaimant(User $user, Application $application): bool
    {
        return $user->hasRole('superadmin') && ! $application->isFormerClaimant($user->id) && $application->isActive();
    }

    public function viewAny(User $user): bool
    {
        return $user->hasOfficeAccess();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Application $application): bool
    {
        return app(ApplicationAccessService::class)->view($user, $application);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ! $user->isInternal();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Application $application): bool
    {
        if ($application->isFormerClaimant($user->id)) {
            return false;
        }

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

    /**
     * Summary of cancel
     */
    public function cancel(User $user, Application $application): bool
    {
        if ($application->isFormerClaimant($user->id)) {
            return false;
        }

        if ($application->closure) {
            return false;
        }

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

    /**
     * Summary of reject
     */
    public function reject(User $user, Application $application): bool
    {
        if ($application->isFormerClaimant($user->id)) {
            return false;
        }

        if ($application->closure) {
            return false;
        }

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

        return $user->canOfficeAction('Create Rejection');
    }

    /**
     * Summary of refer
     */
    public function refer(User $user, Application $application): bool
    {
        if ($application->isFormerClaimant($user->id)) {
            return false;
        }

        if ($application->closure) {
            return false;
        }

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

        return $user->canOfficeAction('Create Referral');
    }

    /**
     * Summary of close
     */
    public function close(User $user, Application $application): bool
    {
        if ($application->isFormerClaimant($user->id)) {
            return false;
        }

        if ($application->closure) {
            return false;
        }

        if (! $application->rejection && ! $application->referral && ! $application->cancellation) {
            return false;
        }

        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->canOfficeAction('Create Closure');
    }
}
