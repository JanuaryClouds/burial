<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sex;

class SexSeeder extends Seeder
{
    public function run(): void
    {
        $sexes = [
            'Female', 
            'Male'
        ];

        foreach ($sexes as $sex) {
            Sex::firstOrCreate([
                'name' => $sex,
                'remarks'   => 'seeder generated',
            ]);
        }
    }
}