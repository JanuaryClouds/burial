<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use App\Models\WorkflowHistory;
use App\Traits\HasSuperadminByPass;
use Illuminate\Auth\Access\Response;

class WorkflowHistoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorkflowHistory $workflowHistory): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Application $application): bool
    {
        if (isset($application->referral)) {
            return false;
        }
        
        if (isset($application->cancellation)) {
            return false;
        }
        
        if ($application->recommendations()->count() == 0) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkflowHistory $workflowHistory): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkflowHistory $workflowHistory): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WorkflowHistory $workflowHistory): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WorkflowHistory $workflowHistory): bool
    {
        return false;
    }
}
