<?php

namespace Database\Seeders;

use App\Models\Barangay;
use App\Models\District;
use App\Models\Relationship;
use App\Models\Religion;
use App\Models\Sex;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeederForTesting extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        District::factory()->count(3)->create();
        Barangay::factory()->count(10)->create();
        Sex::firstOrCreate([
            'name' => 'Male',
        ]);
        Sex::firstOrCreate([
            'name' => 'Female',
        ]);

        Religion::factory()->count(5)->create();

        $relationships = [
            'Father',
            'Mother',
            'Child',
            'Sibling',
            'Spouse',
            'Others'
        ];

        foreach($relationships as $relationship) {
            Relationship::firstOrCreate([
                'name' => $relationship,
            ]);
        }
    }
}
