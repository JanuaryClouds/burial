<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\CentralClientService;
use Illuminate\Database\Seeder;
use RuntimeException;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $centralClientService = new CentralClientService;
        $superAdminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);

        $superadminData = $centralClientService->fetchFromPortal('search', 'user1_uat');
        if (count($superadminData) === 0) {
            throw new RuntimeException('No data found for user1_uat');
        } else {
            $superadminData = $superadminData[0];
        }

        $superadmin = User::create(
            [
                'email' => $superadminData['email'],
                'citizen_uuid' => $superadminData['user_id'],
                'emp_id' => (string) rand(100000, 999999),
                'first_name' => $superadminData['firstname'],
                'middle_name' => $superadminData['middlename'],
                'last_name' => $superadminData['lastname'],
                'suffix' => $superadminData['suffix'] ?? null,
                'is_active' => true,
                'contact_number' => $superadminData['contact_number'],
                'password' => config('app.admin_password'),
            ]
        );

        $superadmin->assignRole($superAdminRole);
        $superadmin->assignRole($staffRole);

        $staffFactories = [];
        for ($staffCount = 2; $staffCount < 6; $staffCount++) {
            $staffDataArray = $centralClientService->fetchFromPortal('search', 'user'.$staffCount.'_uat');
            if (count($staffDataArray) === 0) {
                throw new RuntimeException('No user data found');
            } else {
                $staffData = $staffDataArray[0];

            }
            $staff = User::firstOrCreate([
                'citizen_uuid' => $staffData['user_id'],
                'emp_id' => (string) rand(100000, 999999),
                'email' => $staffData['email'],
                'first_name' => $staffData['firstname'],
                'middle_name' => $staffData['middlename'] ?? null,
                'last_name' => $staffData['lastname'],
                'suffix' => $staffData['suffix'] ?? null,
                'is_active' => true,
                'contact_number' => $staffData['contact_number'],
                'password' => config('app.admin_password'),
            ]);

            $staff->assignRole($staffRole);
            $staffFactories[] = $staff;
        }

        foreach ($staffFactories as $staffFactory) {
            $staffFactory->assignRole($staffRole);
        }

        if (count($staffFactories) > 0) {
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
}
