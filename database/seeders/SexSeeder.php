<?php

namespace Database\Seeders;

use App\Models\Sex;
use Illuminate\Database\Seeder;

class SexSeeder extends Seeder
{
    public function run(): void
    {
        $sexes = [
            'Female',
            'Male',
        ];

        foreach ($sexes as $sex) {
            Sex::firstOrCreate([
                'name' => $sex,
                'remarks' => 'seeder generated',
            ]);
        }
    }
}
