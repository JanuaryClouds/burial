<?php

namespace App\Services; 

use App\Models\Permission;

class PermissionService
{
    public function storePermission(array $data): Permission
    {
        return Permission::create($data);
    }

    public function updatePermission(array $data, $permission): void
    {
        $permission->update($data);
    }

    public function deletePermission($permission): void
    {
        $permission->delete();
    }
}