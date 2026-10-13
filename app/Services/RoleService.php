<?php

namespace App\Services;

// use App\Models\Role;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Models\Role;

class RoleService
{
    /**
     * Summary of storeRole
     */
    public function store(array $data): RoleContract
    {
        if (empty($data['guard_name'])) {
            $data['guard_name'] = 'web';
        }

        return Role::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'],
        ])->syncPermissions($data['permissions']);
    }

    public function updateRole(array $data, Role $role): Role
    {
        $role->update($data);

        return $role;
    }

    /**
     * Summary of destroyRole
     */
    public function destroyRole(Role $role): bool
    {
        return $role->delete();
    }
}
