<?php

namespace Database\Seeders;

use App\Models\CivilStatus;
use Illuminate\Database\Seeder;

class CivilSeeder extends Seeder
{
    public function run(): void
    {
        $civils = [
            'Single',
            'Divorced',
            'Married',
            'Separated',
            'Widowed',
        ];

        foreach ($civils as $civil) {
            CivilStatus::firstOrCreate([
                'name' => $civil,
            ]);
        }
    }
}
