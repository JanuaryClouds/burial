<?php

namespace Database\Seeders;

use App\Models\Education;
use Illuminate\Database\Seeder;

class EducationSeeder extends Seeder
{
    public function run(): void
    {
        $educations = [
            'No Formal Education',
            'Elementary / Primary Level',
            'Junior High School Level',
            'Senior High School Level',
            'Vocational (TESDA)',
            'College Level (Undergraduate)',
            'College Level (Graduate)',
            'Postgraduate Level (Master and Doctorate)'
        ];

        foreach ($educations as $education) {
            Education::firstOrCreate([
                'name' => $education,
                'remarks' => 'seeder generated',
            ]);
        }
    }
}
