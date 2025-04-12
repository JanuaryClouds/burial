<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Education;

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
                'remarks'   => 'seeder generated',
            ]);
        }
    }
}