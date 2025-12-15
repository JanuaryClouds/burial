<?php

namespace Database\Seeders;

use App\Models\Claimant;
use App\Models\Relationship;
use Illuminate\Database\Seeder;

class ClaimantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $relationships = Relationship::pluck('id');

        $claimantSamples = [
            [
                'given_name' => 'Pedro',
                'middle_name' => 'Cruz',
                'last_name' => 'Reyes',
                'suffix' => null,
                'relationship_to_deceased' => $relationships->random(),
                'mobile_number' => '09171234567',
            ],
            [
                'given_name' => 'Ana',
                'middle_name' => 'Lopez',
                'last_name' => 'Garcia',
                'suffix' => null,
                'relationship_to_deceased' => $relationships->random(),
                'mobile_number' => '09179876543',
            ],
            [
                'given_name' => 'Carlos',
                'middle_name' => 'Santos',
                'last_name' => 'Dela Cruz',
                'suffix' => 'Jr.',
                'relationship_to_deceased' => $relationships->random(),
                'mobile_number' => '09173456789',
            ],
        ];

        foreach ($claimantSamples as $data) {
            Claimant::firstOrCreate($data);
        }
    }
}
