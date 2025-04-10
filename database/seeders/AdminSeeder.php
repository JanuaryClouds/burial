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

        $admin = User::firstOrCreate(
            ['email' => 'jsanguyo1624@gmail.com'],
            [
                'first_name'     => 'Jerry',
                'middle_name'    => 'Gonzaga',
                'last_name'      => 'Sanguyo',
                'contact_number' => '09271852710',
                'password'       => bcrypt('password'),
            ]
        );
        
        $admin->assignRole($superAdminRole);
    }
}