<?php

namespace Database\Seeders;

use App\Models\Barangay;
use App\Models\Relationship;
use App\Models\BurialAssistanceRequest;
use Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BurialRequestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangays = Barangay::pluck('id');
        $relationships = Relationship::pluck('id');
        $sampleBurialRequests = [
            [
                'uuid' => Str::uuid()->toString(),
                'deceased_lastname' => 'Dela Cruz',
                'deceased_firstname' => 'Juan',
                'representative' => 'Representative Name',
                'representative_phone' => '09123456789',
                'representative_email' => 'sample@domain.com',
                'representative_relationship' => $relationships->random(),
                'burial_address' => '123 Main St',
                'barangay_id' => $barangays->random(),
                'start_of_burial' => '2025-08-25 00:00:00',
                'end_of_burial' => '2025-08-28 00:00:00',
                'type_of_assistance' => 8,
                'status' => 'pending',
                'service_id' => null,
                'remarks' => 'Seeder Generated',
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'deceased_lastname' => 'Delos Santos',
                'deceased_firstname' => 'Fe',
                'representative' => 'Representative Name',
                'representative_phone' => '09987654321',
                'representative_email' => '',
                'representative_relationship' => $relationships->random(),
                'burial_address' => '123 Second St',
                'barangay_id' => $barangays->random(),
                'start_of_burial' => '2025-08-28 00:00:00',
                'end_of_burial' => '2025-08-30 00:00:00',
                'type_of_assistance' => 8,
                'status' => 'approved',
                'service_id' => null,
                'remarks' => 'Seeder Generated',
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'deceased_lastname' => 'Mantos',
                'deceased_firstname' => 'Romeo',
                'representative' => 'Representative Name',
                'representative_phone' => '09123456789',
                'representative_email' => 'sample@domain.com',
                'representative_relationship' => $relationships->random(),
                'burial_address' => '123 Third St',
                'barangay_id' => $barangays->random(),
                'start_of_burial' => '2025-08-22 00:00:00',
                'end_of_burial' => '2025-08-25 00:00:00',
                'type_of_assistance' => 8,
                'status' => 'rejected',
                'service_id' => null,
                'remarks' => 'Seeder Generated',
            ],
        ];

        foreach ($sampleBurialRequests as $request) {
            BurialAssistanceRequest::firstOrCreate($request);
        }
    }
}
