<?php

namespace App\Services;

use App\Models\Permission;

class PermissionService
{
    /**
     * Summary of storePermission
     */
    public function storePermission(array $data): Permission
    {
        return Permission::create($data);
    }

    /**
     * Summary of updatePermission
     */
    public function updatePermission(array $data, Permission $permission): bool
    {
        return $permission->update($data);
    }

    /**
     * Summary of deletePermission
     */
    public function deletePermission(Permission $permission): bool
    {
        return $permission->delete();
    }
}
