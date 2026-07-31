<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Beneficiary;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BeneficiarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereDoesntHave('roles')->get();

        foreach ($users as $user) {
            if (rand(0, 10) >= 2) {
                Beneficiary::factory()->create([
                    'created_by' => $user->id,
                ]);
            }
        }
    }
}
