<?php

namespace App\Services;

use App\Models\Permission;

class PermissionService
{
    /**
     * Summary of storePermission
     * @param array $data
     * @return Permission
     */
    public function storePermission(array $data): Permission
    {
        return Permission::create($data);
    }

    /**
     * Summary of updatePermission
     * @param array $data
     * @param Permission $permission
     * @return bool
     */
    public function updatePermission(array $data, Permission $permission): bool
    {
        return $permission->update($data);
    }

    /**
     * Summary of deletePermission
     * @param Permission $permission
     * @return bool
     */
    public function deletePermission(Permission $permission): bool
    {
        return $permission->delete();
    }
}
