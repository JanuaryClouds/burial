<?php

namespace App\Services;

use App\Models\Role;

class RoleService
{
    public function storeRole(array $data): Role
    {
        return Role::create($data);
    }

    public function updateRole(array $data, $role): void
    {
        $role->update($data);
    }

    public function destroyRole($role): void
    {
        $role->delete();
    }
}