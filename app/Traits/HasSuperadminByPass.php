<?php

namespace App\Traits;

use App\Models\User;

trait HasSuperadminByPass
{
    public function canSuperadminByPass(User $user): bool
    {
        return $user->hasRole('superadmin');
    }
}
