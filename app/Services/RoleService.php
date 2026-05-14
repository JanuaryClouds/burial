<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Contracts\Role as RoleContract;

class RoleService
{
    /**
     * Summary of storeRole
     * @param array $data data to store
     */
    public function storeRole(array $data): RoleContract
    {
        if (empty($data['guard_name'])) {
            $data['guard_name'] = 'web';
        }

        return Role::create($data);
    }

    /**
     * Summary of updateRole
     * @param array $data data to update
     * @param mixed $role model to update
     * @return Role updated model
     */
    public function updateRole(array $data, $role): Role
    {
        return $role->update($data);
    }

    /**
     * Summary of destroyRole
     * @param mixed $role model to delete
     * @return void
     */
    public function destroyRole($role): void
    {
        $role->delete();
    }
}
