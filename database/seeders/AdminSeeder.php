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

        for ($staffCount = 2; $staffCount < 6; $staffCount++) {
            $staffDataArray = $centralClientService->fetchFromPortal('search', 'user'.$staffCount.'_uat');
            if (count($staffDataArray) === 0) {
                throw new RuntimeException('No user data found');
            } else {
                $staffData = $staffDataArray[0];

            }
            $staff = User::firstOrCreate([
                'citizen_uuid' => $staffData['user_id'],
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

        foreach ($staffFactories as $staff) {
            $allRoles = Role::where('name', '!=', 'superadmin')->pluck('name')->toArray();
            $existingRoles = $staff->roles()->pluck('name')->toArray();
            while (rand(0, 1) === 1 && count($existingRoles) < count($allRoles)) {
                $randomRole = Role::whereNotIn('name', $existingRoles)->inRandomOrder()->first();
                $staff->assignRole($randomRole);
                $existingRoles[] = $randomRole->name;
            }
        }
    }
}
