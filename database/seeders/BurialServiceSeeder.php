<?php

namespace Database\Seeders;

use App\Models\Barangay;
use App\Models\BurialService;
use App\Models\BurialServiceProvider;
use App\Models\Relationship;
use Illuminate\Database\Seeder;

class BurialServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangays = Barangay::pluck('id');
        $relationships = Relationship::pluck('id');
        $providers = BurialServiceProvider::pluck('id');
        $sampleBurialServices = [
            [
                'deceased_lastname' => 'Dela Cruz',
                'deceased_firstname' => 'Juan',
                'representative' => 'Representative Name',
                'representative_phone' => '09123456789',
                'representative_email' => 'representative@domain.com',
                'representative_relationship' => $relationships->random(),
                'burial_address' => '123 Main St',
                'barangay_id' => $barangays->random(),
                'start_of_burial' => '2025-08-25 00:00:00',
                'end_of_burial' => '2025-08-28 00:00:00',
                'burial_service_provider' => $providers->random(),
                'collected_funds' => '1000',
                'remarks' => 'Seeder Generated',
            ],
            [
                'deceased_lastname' => 'Dela Rosa',
                'deceased_firstname' => 'Maria',
                'representative' => 'Representative Name',
                'representative_phone' => '09987654321',
                'representative_email' => null,
                'representative_relationship' => $relationships->random(),
                'burial_address' => '123 Second St',
                'barangay_id' => $barangays->random(),
                'start_of_burial' => '2025-08-19 00:00:00',
                'end_of_burial' => '2025-08-21 00:00:00',
                'burial_service_provider' => $providers->random(),
                'collected_funds' => '1125',
                'remarks' => 'Seeder Generated',
            ],
            [
                'deceased_lastname' => 'Santos',
                'deceased_firstname' => 'Brie',
                'representative' => 'Representative Name',
                'representative_phone' => '09567890123',
                'representative_email' => 'representative@domain.com',
                'representative_relationship' => $relationships->random(),
                'burial_address' => '123 Third St',
                'barangay_id' => $barangays->random(),
                'start_of_burial' => '2025-08-26 00:00:00',
                'end_of_burial' => '2025-08-29 00:00:00',
                'burial_service_provider' => $providers->random(),
                'collected_funds' => '1500',
                'remarks' => 'Seeder Generated',
            ],
        ];

        foreach ($sampleBurialServices as $service) {
            BurialService::firstOrCreate($service);
        }
    }
}
