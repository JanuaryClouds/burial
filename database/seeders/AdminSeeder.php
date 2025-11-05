<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $deptAdminRole = Role::firstOrCreate(['name' => 'deptAdmin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@email.com'],
            [
                'first_name'     => 'Super',
                'middle_name'    => 'System',
                'last_name'      => 'Admin',
                'contact_number' => '09123456789',
                'password'       => bcrypt('funeral.password'),
            ]
        );

        $deptAdmin = User::firstOrCreate(
            ['email' => 'deptadmin@email.com'],
            [
                'first_name'     => 'Department',
                'middle_name'    => 'Admin',
                'last_name'      => 'Admin',
                'contact_number' => '09987654321',
                'password'       => bcrypt('funeral.password'),
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@email.com'],
            [
                'first_name'     => 'Regular',
                'middle_name'    => null,
                'last_name'      => 'Admin',
                'contact_number' => '09234567891',
                'password'       => bcrypt('funeral.password'),
            ]
        );

        $superadmin->assignRole($superAdminRole);
        $deptAdmin->assignRole($deptAdminRole);
        $admin->assignRole($adminRole);

        $adminFactories = User::factory()->count(5)->create();
        foreach ($adminFactories as $adminFactory) {
            $adminFactory->assignRole($adminRole);
        }

    }
}