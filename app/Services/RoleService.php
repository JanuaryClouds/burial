<?php

namespace App\Services;

// use App\Models\Role;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function storeRole(array $data): Role
    {
        if (empty($data['guard_name'])) {
            $data['guard_name'] = 'web';
        }

        return Role::create($data);
    }

    public function updateRole(array $data, $role): Role
    {
        if ($role->update($data)) {
            return $role;
        }

        return null;
    }

    public function destroyRole($role): Role
    {
        if ($role->delete()) {
            return $role;
        }

        return null;
    }
}
