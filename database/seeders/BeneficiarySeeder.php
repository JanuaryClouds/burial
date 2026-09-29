<?php

namespace Database\Seeders;

use App\Models\Beneficiary;
use App\Models\User;
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
                $isFromPateros = rand(0, 9) >= 8;

                if ($isFromPateros) {
                    $provinceCode = '817';
                    $municipalityCode = '1';
                    $barangayCode = (string) rand(1, 10);
                } else {
                    $provinceCode = '815';
                    $municipalityCode = null;
                    $barangayCode = (string) rand(1, 38);
                }

                Beneficiary::factory()->create([
                    'created_by' => $user->id,
                    'province_code' => $provinceCode,
                    'municipality_code' => $municipalityCode,
                    'barangay_code' => $barangayCode,
                ]);
            }
        }
    }
}
