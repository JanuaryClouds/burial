<?php

namespace Database\Seeders;

use App\Models\Religion;
use Illuminate\Database\Seeder;

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
                'remarks' => 'seeder generated',
            ]);
        }
    }
}
