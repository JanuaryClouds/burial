<?php

namespace App\Policies;

use App\Models\User;

class ReportPolicy
{
    public function view(User $user): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->canOfficeAction('View Report');
    }

    public function create(User $user): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->canOfficeAction('Create Report');
    }
}
