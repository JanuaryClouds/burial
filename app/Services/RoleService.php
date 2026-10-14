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

        $permissions = explode(',', $data['permissions']);

        return Role::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'],
        ])->syncPermissions($permissions);
    }

    public function update(array $data, Role $role): Role
    {
        $permissions = explode(',', $data['permissions']);

        $role->syncPermissions($permissions);

        $role->update([
            'name' => $data['name'],
        ]);

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
