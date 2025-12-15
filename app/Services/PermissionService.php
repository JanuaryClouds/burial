<?php

namespace App\Services;

use App\Models\Permission;

class PermissionService
{
    public function storePermission(array $data): Permission
    {
        return Permission::create($data);
    }

    public function updatePermission(array $data, $permission): Permission
    {
        if ($permission->update($data)) {
            return $permission;
        }

        return null;
    }

    public function deletePermission($permission): Permission
    {
        if ($permission->delete()) {
            return $permission;
        }

        return null;
    }
}
