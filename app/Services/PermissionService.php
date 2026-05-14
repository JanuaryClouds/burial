<?php

namespace App\Services;

use App\Models\Permission;

class PermissionService
{
    /**
     * Summary of storePermission
     * @param array $data data to store
     * @return Permission
     */
    public function storePermission(array $data): Permission
    {
        return Permission::create($data);
    }

    /**
     * Summary of updatePermission
     * @param array $data data to update
     * @param mixed $permission model to update
     * @return Permission
     */
    public function updatePermission(array $data, $permission): Permission
    {
        return $permission->update($data);
    }

    /**
     * Summary of deletePermission
     * @param mixed $permission model to delete
     * @return void
     */
    public function deletePermission($permission): void
    {
        $permission->delete();
    }
}
