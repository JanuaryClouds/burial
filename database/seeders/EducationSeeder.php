<?php

namespace Database\Seeders;

use App\Models\Education;
use Illuminate\Database\Seeder;

class EducationSeeder extends Seeder
{
    public function run(): void
    {
        $educations = [
            'Bachelor Degree',
            'Secondary',
            'Primary',
        ];

        foreach ($educations as $education) {
            Education::firstOrCreate([
                'name' => $education,
                'remarks' => 'seeder generated',
            ]);
        }
    }
}
