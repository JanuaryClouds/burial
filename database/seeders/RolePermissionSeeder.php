<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
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
            Permission::firstOrCreate([
                'id' => (string) Str::uuid(),
                'name' => $permission
            ]);
        }

        foreach ($otherPermissions as $permission) {
            Permission::firstOrCreate([
                'id' => (string) Str::uuid(),
                'name' => $permission
            ]);
        }

        Role::firstOrCreate([
            'id' => (string) Str::uuid(),
            'name' => 'superadmin'
        ]);

        // Default Role to all
        Role::firstOrCreate([
            'id' => (string) Str::uuid(),
            'name' => 'staff'
        ]);

        $reporterRole = Role::firstOrCreate([
            'id' => (string) Str::uuid(),
            'name' => 'reporter'
        ]);

        $reporterRole->givePermissionTo([
            'create-reports',
            'view-reports',
        ]);

        $interviewerRole = Role::firstOrCreate([
            'id' => (string) Str::uuid(),
            'name' => 'interviewer'
        ]);

        $interviewerRole->givePermissionTo([
            'create-interview-schedules',
            'create-assessments',
            'create-recommendations',
            'create-referrals',
        ]);

        $burialStaffRole = Role::firstOrCreate([
            'id' => (string) Str::uuid(),
            'name' => 'burial-staff'
        ]);

        $burialStaffRole->givePermissionTo([
            'view-burial-assistances',
            'create-updates',
            'create-claimant-change-requests',
            'edit-claimant-change-requests',
            'create-certificates',
        ]);

        $librengLibingStaffRole = Role::firstOrCreate([
            'id' => (string) Str::uuid(),
            'name' => 'libreng-libing-staff'
        ]);
        
        $librengLibingStaffRole->givePermissionTo([
            'view-libreng-libings',
            'update-libreng-libings',
            'create-certificates',
        ]);
    }
}
