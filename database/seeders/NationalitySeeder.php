<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Nationality;

class NationalitySeeder extends Seeder
{
    public function run(): void
    {
        $Nationalities = [
            'Filipino',
            'American',
            'Canadian',
            'Australian',
        ];

        foreach ($Nationalities as $Nationality) {
            Nationality::firstOrCreate([
                'name' => $Nationality,
                'remarks'   => 'seeder generated',
            ]);
        }
    }
}