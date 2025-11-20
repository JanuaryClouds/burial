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
            'add-updates', // IDEA: for models, specify the CRUD actions
            'view-logs',
            'manage-content',
            'manage-accounts',
            'manage-assignments',
            'manage-roles',
            'write-assessments',
            'add-family-members',
            'submit-images',
            // TODO: Add more GIS-related permissions
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $role = [
            'superadmin' => Permission::all(),
            'deptAdmin' => Permission::where(function($query) {
                $query->where('name', '!=', 'manage-content');
                $query->where('name', '!=', 'manage-accounts');
                $query->where('name', '!=', 'manage-roles');
            })->get(),
            'admin' => Permission::where(function($query) {
                $query->where('name', '!=', 'manage-content');
                $query->where('name', '!=', 'manage-accounts');
                $query->where('name', '!=', 'manage-roles');
                $query->where('name', '!=', 'manage-assignments');
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