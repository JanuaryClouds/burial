<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Religion;

class ReligionSeeder extends Seeder
{
    public function run(): void
    {
        // TODO: Update this seeder to use correct religions
        $Religions = [
            'Son',
            'Daughter',
            'Mother',
            'Father',
            'Grandmother',
            'Grandfather',
            'Cousin',
            'Uncle',
            'Auntie',
        ];

        foreach ($Religions as $Religion) {
            Religion::firstOrCreate([
                'name' => $Religion,
                'remarks'   => 'seeder generated',
            ]);
        }
    }
}