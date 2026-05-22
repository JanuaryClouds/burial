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
            // 'create-clients',
            'create-interview-schedules',
            'create-assessments',
            'create-recommendations',
            'create-referrals',
            'create-certificates',

            // Burial Assistance Models
            'view-burial-assistances',
            'create-updates',
            'create-claimant-change-requests',
            'edit-claimant-change-requests',
            // 'delete-updates',

            // Libreng Libing
            'view-libreng-libings',
            'update-libreng-libings',

            // Reports
            'create-reports',
            'view-reports',
        ];

        $otherPermissions = [
            // Logs
            'view-logs',

            // CMS only for superadmin
            // 'manage-content',

            // System
            // 'create-users',
            // 'view-users',
            // 'edit-users',
            'create-roles',
            'view-roles',
            'edit-roles',
            // 'edit-system-settings', // Only for superadmin
        ];

        foreach ($staffPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        foreach ($otherPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        Role::firstOrCreate(['name' => 'superadmin']);

        // Default Role to all
        Role::firstOrCreate(['name' => 'staff']);
        
        $reporterRole = Role::firstOrCreate(['name' => 'reporter']);
        $reporterRole->givePermissionTo([
            'create-reports',
            'view-reports'
        ]);
        
        $interviewerRole = Role::firstOrCreate(['name' => 'interviewer']);
        $interviewerRole->givePermissionTo([
            'create-interview-schedules',
            'create-assessments',
            'create-recommendations',
            'create-referrals',
        ]);

        $burialStaffRole = Role::firstOrCreate(['name' => 'burial-staff']);
        $burialStaffRole->givePermissionTo([
            'view-burial-assistances',
            'create-updates',
            'create-claimant-change-requests',
            'edit-claimant-change-requests',
            'create-certificates'
        ]);
        
        $librengLibingStaffRole = Role::firstOrCreate(['name' => 'libreng-libing-staff']);
        $librengLibingStaffRole->givePermissionTo([
            'view-libreng-libings',
            'update-libreng-libings',
            'create-certificates',
        ]);
    }
}
