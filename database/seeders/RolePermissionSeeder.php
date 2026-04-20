<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $staffPermissions = [
            // Client Models
            'create-clients',
            'view-clients',
            'create-interview-schedules',
            'create-assessments',
            'create-recommendations',
            'create-referrals',

            // Burial Assistance Models
            'view-burial-assistances',
            'create-burial-assistances',
            'create-updates',
            'create-claimant-change-requests',
            'edit-claimant-change-requests',
            'delete-updates',

            // Libreng Libing
            'create-libreng-libings',
            'view-libreng-libings',
            'update-libreng-libings',

            // Reports
            'create-reports',
            'view-reports',
        ];

        $otherPermissions = [
            // Logs
            'view-logs',

            // CMS
            'manage-content',

            // System
            'create-users',
            'view-users',
            'edit-users',
            'create-roles',
            'view-roles',
            'edit-roles',
            'edit-system-settings',
        ];

        foreach ($staffPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        foreach ($otherPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        Role::firstOrCreate(['name' => 'superadmin']);

        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $staffRole->givePermissionTo($staffPermissions);
    }
}
