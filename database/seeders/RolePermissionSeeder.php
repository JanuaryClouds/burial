<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'create',
            'edit',
            'delete',
            'view'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $role = [
            'superadmin' => Permission::all(),
            'admin' => Permission::all(),
            'user' => 'view'
        ];

        foreach($role as $roleName => $perm)
        {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($perm);
        }
    }
}