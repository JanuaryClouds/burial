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
             'contact_details' => '123-456-7890', 
             'address' => '123 Sample St',
             'barangay_id' => $barangays->random(),
             'remarks' => 'Seeder Generated'
            ],
            ['name' => 'Sample Provider 2',
             'contact_details' => '456-789-0123', 
             'address' => '234 Sample St',
             'barangay_id' => $barangays->random(),
             'remarks' => 'Seeder Generated'
            ],
            ['name' => 'Sample Provider 3',
             'contact_details' => '789-012-3456', 
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
