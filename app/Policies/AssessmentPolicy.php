<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use App\Services\ApplicationAccessService;

class AssessmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canOfficeAction('View Any Assessment');
    }

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

        if ($application->assessment) {
            return false;
        }

        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->canOfficeAction('Create Assessment');
    }
}
