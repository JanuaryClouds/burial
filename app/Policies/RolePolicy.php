<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSystemAdministrator();
    }

    public function create(User $user): bool
    {
        return $user->isSystemAdministrator();
    }

    public function view(User $user, Role $role): bool
    {
        return $this->update($user, $role);
    }

    public function update(User $user, Role $role): bool
    {
        return $user->isSystemAdministrator() && ! in_array(strtolower($role->name), ['staff', 'cswdo', 'superadmin'], true);
    }

    public function delete(User $user, Role $role): bool
    {
        return $this->update($user, $role);
    }

    public function restore(User $user, Role $role): bool
    {
        return $this->update($user, $role);
    }

    public function forceDelete(User $user, Role $role): bool
    {
        return $this->update($user, $role);
    }
}
