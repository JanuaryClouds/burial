<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereHas('clients')
            ->whereHas('beneficiaries')
            ->get();

        foreach ($users as $user) {
            if (rand(0, 10) >= 2) {
                Application::factory()->create([
                    'client_uuid' => $user->clients->random()->uuid,
                    'beneficiary_uuid' => $user->beneficiaries->random()->uuid,
                ]);
            }
        }

        dump(Application::count().' Applications Seeded');
    }
}
