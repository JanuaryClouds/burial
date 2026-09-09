<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Gate;

trait HasSuperadminByPass
{
    public function canSuperadminByPass(User $user): bool
    {
        return $user->hasRole('superadmin');
    }
}
