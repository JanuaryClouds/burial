<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Legacy Permissions
        // $permissions = [
        //     'create',
        //     'edit',
        //     'delete',
        //     'view',
        //     'view-reports',
        //     'schedule-interviews',
        //     'assess-applications',
        //     'recommend-services',
        //     'reject-applications',
        //     'approve-funeral-services',
        //     'add-updates',
        //     'remove-updates',
        //     'view-logs',
        //     'manage-content',
        //     'manage-roles',
        //     'manage-accounts',
        //     'manage-assignments',
        // ];

        $adminPermissions = [
            'create',
            'edit',
            'delete',
            'view',
            'schedule-interviews',
            'assess-applications',
            'recommend-services',
            'reject-applications',
            'add-updates',
            'delete-updates',
            'view-reports',
        ];

        $deptAdminPermissions = [
            'create',
            'edit',
            'delete',
            'view',
            'schedule-interviews',
            'assess-applications',
            'recommend-services',
            'reject-applications',
            'approve-funeral-services',
            'add-updates',
            'delete-updates',
            'view-logs',
            'view-reports',
            'manage-assignments',
        ];

        $superAdminPermissions = [
            'create',
            'edit',
            'delete',
            'view',
            'schedule-interviews',
            'assess-applications',
            'recommend-services',
            'reject-applications',
            'approve-funeral-services',
            'add-updates',
            'delete-updates',
            'view-logs',
            'view-reports',
            'manage-content',
            'manage-roles',
            'manage-accounts',
            'manage-assignments',
        ];

        foreach ($adminPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        foreach ($deptAdminPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        foreach ($superAdminPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        foreach ($adminPermissions as $permission) {
            $role = Role::firstOrCreate(['name' => 'admin']);
            $role->givePermissionTo($permission);
        }

        foreach ($deptAdminPermissions as $permission) {
            $role = Role::firstOrCreate(['name' => 'deptAdmin']);
            $role->givePermissionTo($permission);
        }

        foreach ($superAdminPermissions as $permission) {
            $role = Role::firstOrCreate(['name' => 'superadmin']);
            $role->givePermissionTo($permission);
        }

        // $role = [
        //     'superadmin' => Permission::all(),
        //     'deptAdmin' => Permission::where(function($query) {
        //         $query->where('name', '!=', 'manage-content');
        //         $query->where('name', '!=', 'manage-accounts');
        //         $query->where('name', '!=', 'manage-roles');
        //     })->get(),
        //     'admin' => Permission::where(function($query) {
        //         $query->where('name', '!=', 'manage-content');
        //         $query->where('name', '!=', 'manage-accounts');
        //         $query->where('name', '!=', 'manage-roles');
        //         $query->where('name', '!=', 'manage-assignments');
        //     })->get(),
        //     'user' => 'view'
        // ];

        // foreach($role as $roleName => $perm)
        // {
        //     $role = Role::firstOrCreate(['name' => $roleName]);
        //     $role->syncPermissions($perm);
        // }
    }
}
