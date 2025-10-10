<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class BaoAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // TODO: Please change the credentials to be the official BAO account credentials. Credentials provided are placeholder credetials.
        $userRole = Role::firstOrCreate(['name' => 'admin']);

        $baoAccount = User::firstOrCreate(
            ['email' => 'janmichaelcristianbcruz@gmail.com'],
            [
                'first_name' => 'Jan Michael Cristian', 
                'middle_name' => 'Brequillo', 
                'last_name' => 'Cruz',
                'contact_number' => '09665691880',
                'password' => bcrypt('bao.password'),
            ]
        );
        $adminFactories = User::factory()->count(5)->create();
        foreach ($adminFactories as $admin) {
            $admin->assignRole($userRole);
        }
        $baoAccount->assignRole($userRole);
    }
}
