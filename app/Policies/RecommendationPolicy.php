<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use App\Services\ApplicationAccessService;

class RecommendationPolicy
{
    public function view(User $user, ?Application $application = null): bool
    {
        return $application ? $user->isInternal() && app(ApplicationAccessService::class)->view($user, $application) : $user->hasOfficeAccess();
    }

    public function create(User $user, Application $application): bool
    {
        if ($application->isFormerClaimant($user->id) || ! $application->isActive() || ! $application->finishedInterview()) {
            return false;
        }

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

        if (! empty(array_intersect(collect($application->status())->pluck('label')->toArray(), ['releasing', 'referred', 'cancelled', 'closed']))) {
            return false;
        }

        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->canOfficeAction('Create Recommendation');
    }
}
