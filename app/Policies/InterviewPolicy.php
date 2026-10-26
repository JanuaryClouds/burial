<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\Client;
use App\Models\Interview;
use App\Models\User;

class InterviewPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->loadMissing('roles')->roles->isNotEmpty()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Interview $interview): bool
    {
        if ($user->loadMissing('roles')->roles->isNotEmpty()) {
            return true;
        }

        return $user->id == $interview->client->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Client $client): bool
    {
        $application = $client->application()->first();
        if ($application && (! $application->isActive() || $application->current_recommendation_uuid
            || $application->finishedInterview() || $application->currentInterviews()->where('status', 'scheduled')->exists()
            || $application->isFormerClaimant($user->id))) {
            return false;
        }
        if ($client->isUsed() && ! $client->application()->exists()) {
            return false;
        }

        if ($user->id === $client->user_id) {
            return false;
        }

        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('Create Interview');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function complete(User $user, Interview $interview, ?Application $application = null): bool
    {
        if ($application) {
            if ($interview->client_uuid !== $application->client_uuid || $interview->claimant_assignment_uuid !== $application->current_claimant_assignment_uuid) {
                return false;
            }
            $application->loadMissing('client');
            $clientUserId = $application->client->user_id;
        } else {
            $interview->loadMissing(['client', 'claimantAssignment']);
            if ($interview->claimantAssignment?->ended_at || (! $interview->claimant_assignment_uuid && $interview->client->isUsed())) {
                return false;
            }
            $application = $interview->claimantAssignment?->application()->first();
            $clientUserId = $interview->client->user_id;
        }
        if ($interview->status !== 'scheduled' || ($application && (! $application->isActive()
            || $application->current_recommendation_uuid || $application->finishedInterview() || $application->isFormerClaimant($user->id)))) {
            return false;
        }

        return $user->id !== $clientUserId && ($user->hasRole('superadmin') || $user->can('Create Interview'));
    }

    public function update(User $user, Interview $interview): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Interview $interview): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Interview $interview): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Interview $interview): bool
    {
        return false;
    }
}
