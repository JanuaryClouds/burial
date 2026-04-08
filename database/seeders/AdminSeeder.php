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
            ['email' => 'superadmin@email.com'],
            [
                'first_name' => 'Super',
                'middle_name' => 'System',
                'last_name' => 'Admin',
                'contact_number' => '09123456789',
                'password' => config('app.admin_password'),
            ]
        );

        $superadmin->assignRole($superAdminRole);

        $staffFactories = User::factory()->count(5)->create([
            'password' => config('app.admin_password'),
        ]);

        foreach ($staffFactories as $staffFactory) {
            $staffFactory->assignRole($staffRole);
        }

    }
}
