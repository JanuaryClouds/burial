<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class AssessmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyDirectPermission(Permission::where('name', 'like', '% Assessment')->pluck('name')->all());
    }

    public function view(User $user): bool
    {
        if ($user->loadMissing('roles')->roles->isNotEmpty()) {
            return true;
        }

        return false;
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

        return $user->hasDirectPermission('Create Assessment');
    }
}
