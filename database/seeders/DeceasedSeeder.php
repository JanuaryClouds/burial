<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Deceased;
use App\Models\Barangay;
use App\Models\Sex;

class DeceasedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangays = Barangay::pluck('id');
        $genders = Sex::pluck('id');

        $deceasedData = [
            [
                'given_name' => 'Juan',
                'middle_name' => 'Santos',
                'last_name' => 'Dela Cruz',
                'suffix' => null,
                'date_of_birth' => '1950-01-15',
                'date_of_death' => '2025-08-20',
                'gender' => $genders->random(),
                'address' => '123 Main St',
                'barangay_id' => $barangays->random(),
            ],
            [
                'given_name' => 'Maria',
                'middle_name' => 'Lopez',
                'last_name' => 'Dela Rosa',
                'suffix' => null,
                'date_of_birth' => '1960-05-30',
                'date_of_death' => '2025-08-18',
                'gender'=> $genders->random(),
                'address' => '456 Second St',
                'barangay_id' => $barangays->random(),
            ],
            [
                'given_name'=> 'David',
                'middle_name' => 'Garcia',
                'last_name' => 'Santos',
                'suffix' => 'Jr.',
                'date_of_birth' => '1945-12-10',
                'date_of_death' => '2025-08-22',
                'gender' => $genders->random(),
                'address' => '789 Third St',
                'barangay_id' => $barangays->random(),
            ],
        ];

        foreach ($deceasedData as $data) {
            Deceased::firstOrCreate($data);
        }
    }
}
