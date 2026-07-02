<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);

        $superadmin = User::firstOrCreate(
            [
                'email' => 'superadmin@example.org',
                'first_name' => 'Super',
                'middle_name' => null,
                'last_name' => 'Admin',
                'suffix' => null,
                'is_active' => true,
                'contact_number' => null,
                'password' => config('app.admin_password'),
            ]
        );

        $superadmin->assignRole($superAdminRole);
        $superadmin->assignRole($staffRole);

        $staffFactories = [];
        for ($staffCount = 1; $staffCount < 6; $staffCount++) {
            $staff = User::firstOrCreate([
                'emp_id' => null,
                'email' => 'staff' . $staffCount . '@example.org',
                'first_name' => 'Staff',
                'middle_name' => null,
                'last_name' => 'User ' . $staffCount,
                'suffix' => null,
                'is_active' => true,
                'contact_number' => null,
                'password' => config('app.admin_password'),
            ]);

            $staff->assignRole($staffRole);
            $staffFactories[] = $staff;
        }

        foreach ($staffFactories as $staff) {
            $assignedRoles = $staff->roles()->pluck('name')->toArray();
            $unavailableRoles = array_merge($assignedRoles, ['superadmin', 'staff']);

            $availableRoles = Role::whereNotIn('name', $unavailableRoles)->pluck('name')->toArray();
            $assignedRolesCount = 0;

            while ($assignedRolesCount < count($availableRoles)) {
                if (count($assignedRoles) >= count($availableRoles) || rand(0, 1) === 0) {
                    break;
                }

                $randomRole = array_rand($availableRoles);
                if (in_array($availableRoles[$randomRole], $assignedRoles)) {
                    continue;
                }

                $staff->assignRole($availableRoles[$randomRole]);
                $assignedRoles[] = $availableRoles[$randomRole];
                $assignedRolesCount++;
            }
        }
    }
}
