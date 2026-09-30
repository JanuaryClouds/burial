<?php

namespace Database\Seeders;

use App\Models\Address;
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
                $isFromPateros = rand(0, 9) >= 8;

                if ($isFromPateros) {
                    $provinceCode = '817';
                    $provinceName = null;
                    $municipalityCode = '1';
                    $municipalityName = 'Pateros';
                    $barangayCode = (string) rand(1, 10);
                    $barangayName = app(BarangaySeeder::class)->paterosBarangays()[(int) $barangayCode - 1];
                } else {
                    $provinceCode = '815';
                    $provinceName = 'City of Taguig';
                    $municipalityCode = null;
                    $municipalityName = null;
                    $barangayCode = (string) rand(1, 38);
                    $barangayName = app(BarangaySeeder::class)->taguigBarangays()[(int) $barangayCode - 1];
                }

                $client = Client::factory()->create([
                    'user_id' => $user->id,
                ]);

                Address::factory()->create([
                    'addressable_type' => Client::class,
                    'addressable_id' => $client->uuid,
                    'region_code' => '13',
                    'region_name' => 'National Capital Region (NCR)',
                    'province_code' => $provinceCode,
                    'province_name' => $provinceName,
                    'municipality_code' => $municipalityCode,
                    'municipality_name' => $municipalityName,
                    'barangay_code' => $barangayCode,
                    'barangay_name' => $barangayName,
                ]);
            }
        }
    }
}
