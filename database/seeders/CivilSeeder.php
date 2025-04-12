<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CivilStatus;

class CivilSeeder extends Seeder
{
    public function run(): void
    {
        $civils = [
            'Single',
            'Married',
            'Widowed',
            'Separated'
        ];

        foreach ($civils as $civil) {
            CivilStatus::firstOrCreate([
                'name' => $civil,
                'remarks'   => 'seeder generated',
            ]);
        }
    }
}