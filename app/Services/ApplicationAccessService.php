<?php

namespace App\Services;

use App\Models\Application;
use App\Models\User;

class ApplicationAccessService
{
    public function workflow(User $user, Application $application): bool
    {
        if (! $user->isInternal()) {
            return false;
        }
        $application->loadMissing(['activeRecommendation.workflow', 'workflowStage']);
        $run = $application->activeRecommendation;
        $workflow = $run?->workflow;
        if (! $workflow || $run->claimant_assignment_uuid !== $application->current_claimant_assignment_uuid) {
            return false;
        }
        $current = $application->workflowStage;
        if ($current && ($current->workflow_uuid !== $workflow->uuid || $current->trashed() || ! $current->position)) {
            return false;
        }
        $position = $current->position ?? 0;
        $permissions = $user->effectivePermissionNames();
        // Several guards and read-only partials authorize the same snapshot.
        // Reuse only within this request; a new stage, run, claimant, or permission
        // set has a different key and every poll starts with an empty request.
        $key = 'burial.workflow_access.'.hash('sha256', serialize([
            $user->getKey(), $application->getKey(), $application->current_claimant_assignment_uuid,
            $run->getKey(), $workflow->getKey(), $current?->getKey(), $position, $permissions,
        ]));
        if (request()->attributes->has($key)) {
            return request()->attributes->get($key);
        }

        $allowed = $workflow->stages()->where(function ($query) use ($position) {
            $query->where('position', $position + 1);
            if ($position > 0) {
                $query->orWhere('position', '<=', $position);
            }
        })->whereHas('permission', fn ($query) => $query->whereIn('name', $permissions))->exists();
        request()->attributes->set($key, $allowed);

        return $allowed;
    }

    public function view(User $user, Application $application): bool
    {
        if ($user->hasOfficeAccess()) {
            return true;
        }
        if ($user->isInternal()) {
            return $this->workflow($user, $application);
        }

        return $application->client()->where('user_id', $user->id)->exists()
            || $application->claimantAssignments()->whereHas('client', fn ($query) => $query->where('user_id', $user->id))->exists();
    }
}
