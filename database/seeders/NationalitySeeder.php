<?php

namespace Database\Seeders;

use App\Models\Nationality;
use Illuminate\Database\Seeder;

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
                'remarks' => 'seeder generated',
            ]);
        }
    }
}
