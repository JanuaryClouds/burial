<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use App\Models\WorkflowStage;
use App\Traits\HasSuperadminByPass;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Permission;

class AssessmentPolicy
{
    public array $permissions = [];

    public function __construct()
    {
        $permissions = Permission::where('name', 'like', 'assessment.%')->get();
        foreach ($permissions as $permission) {
            $this->permissions[] = $permission->name;
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

        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->hasDirectPermission('assessment.create');
    }
}
