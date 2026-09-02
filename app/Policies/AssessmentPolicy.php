<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use App\Models\WorkflowStage;
use Spatie\Permission\Models\Permission;

class AssessmentPolicy
{
    public array $permissions = [];

    public function __construct()
    {
        $permissions = Permission::where('name', 'like', 'workflow.assessment.%')->get();
        foreach ($permissions as $permission) {
            $this->permissions[] = $permission;
        }
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyDirectPermission($this->permissions);
    }

    public function view(User $user): bool
    {
        return $user->hasAnyDirectPermission($this->permissions);
    }

    public function create(User $user, Application $application): bool
    {
        if ($user->id === $application->client->user_id) {
            return false;
        }

        if ($application->assessment) {
            return false;
        }

        $assessmentStageUuid = WorkflowStage::firstWhere('name', 'assessment')->uuid;

        if ($application->current_workflow_stage_uuid !== $assessmentStageUuid) {
            return false;
        }

        return $user->hasDirectPermission(
            array_search('create', $this->permissions, false)
        );
    }
}
