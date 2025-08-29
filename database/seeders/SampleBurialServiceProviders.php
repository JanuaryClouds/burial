<?php

namespace Database\Seeders;

use App\Models\Barangay;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SampleBurialServiceProviders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangays = Barangay::pluck('id');

        $sampleBurialServiceProviders = [
            ['name' => 'Sample Provider 1',
             'phone' => '123-456-7890',
             'email' => 'sample.provider1@domain.com', 
             'address' => '123 Sample St',
             'barangay_id' => $barangays->random(),
             'remarks' => 'Seeder Generated'
            ],
            ['name' => 'Sample Provider 2',
            'phone' => '456-789-0123', 
            'email' => 'sample.provider2@domain.com', 
            'address' => '234 Sample St',
            'barangay_id' => $barangays->random(),
            'remarks' => 'Seeder Generated'
        ],
            ['name' => 'Sample Provider 3',
            'phone' => '789-012-3456', 
            'email' => 'sample.provider3@domain.com', 
             'address' => '567 Sample St',
             'barangay_id' => $barangays->random(),
             'remarks' => 'Seeder Generated'
            ],
        ];

        foreach ($sampleBurialServiceProviders as $provider) {
            \App\Models\BurialServiceProvider::firstOrCreate($provider);
        }
    }
}
