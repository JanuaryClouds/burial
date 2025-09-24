<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Religion;

class ReligionSeeder extends Seeder
{
    public function run(): void
    {
        $Religions = [
            'Catholic',
            'Muslim',
            'Iglesia ni Cristo',
            'Philippine Independent Church',
            'Seventh-day Adventist',
            'Bible Baptist Church',
            'United Church of Christ',
            'Jehova\'s Witnesses',
            'Church of Christ',
            'Others',
            'None',
        ];

        foreach ($Religions as $Religion) {
            Religion::firstOrCreate([
                'name' => $Religion,
                'remarks'   => 'seeder generated',
            ]);
        }
    }
}