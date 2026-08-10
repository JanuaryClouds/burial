<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereDoesntHave('roles')->get();

        if (app()->isProduction()) {
            dump('[!] WARNING: Seeding database while inside a Production Environment');
        }

        foreach ($users as $user) {
            if (rand(0, 10) >= 2) {
                Client::factory()->create([
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
