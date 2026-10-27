<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $superadmin = User::firstOrCreate(['email' => 'superadmin@example.org'], [
            'first_name' => 'Super', 'middle_name' => null, 'last_name' => 'Admin',
            'suffix' => null, 'is_active' => true, 'contact_number' => null,
            'password' => config('app.admin_password'),
        ]);
        $superadmin->assignRole(Role::findOrCreate('superadmin', 'web'));
        $superadmin->assignRole(Role::findOrCreate('staff', 'web'));

        // Deterministic staff and departmental fixtures are owned by UserSeeder.
    }
}
