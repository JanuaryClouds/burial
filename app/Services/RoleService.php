<?php

namespace App\Services;

// use App\Models\Role;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Contracts\Role as RoleContract;

class RoleService
{
    /**
     * Summary of storeRole
     * @param array $data
     * @return RoleContract
     */
    public function storeRole(array $data): RoleContract
    {
        if (empty($data['guard_name'])) {
            $data['guard_name'] = 'web';
        }

        return Role::create($data);
    }

    public function updateRole(array $data, Role $role): Role
    {
        $role->update($data);

        return $role;
    }

    /**
     * Summary of destroyRole
     * @param Role $role
     * @return bool
     */
    public function destroyRole(Role $role): bool
    {
        return $role->delete();
    }
}
