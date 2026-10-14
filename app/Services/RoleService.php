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
        ])->syncPermissions(array_keys($data['permissions']));
    }

    public function update(array $data, Role $role): Role
    {
        $role->syncPermissions(array_keys($data['permissions']));

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
