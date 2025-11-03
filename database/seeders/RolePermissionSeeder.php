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
            'view',
            'view-reports',
            'reject-applications',
            'add-updates',
            'view-logs',
            'write-swas',
            'manage-claimant-changes',
            'manage-content',
            'assign'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $role = [
            'superadmin' => Permission::where(function($query) {
                $query->where('name', '==', 'manage-content');
            })->get(),
            'deptAdmin' => Permission::where(function($query) {
                $query->where('name', '!=', 'manage-content');
            })->get(),
            'admin' => Permission::where(function($query) {
                $query->where('name', '!=', 'manage-content');
                $query->where('name', '!=', 'manage-claimant-changes');
                $query->where('name', '!=', 'assign');
            })->get(),
            'user' => 'view'
        ];

        foreach($role as $roleName => $perm)
        {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($perm);
        }
    }
}