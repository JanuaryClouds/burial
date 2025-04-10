<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ModeOfAssistance;

class MoaSeeder extends Seeder
{
    public function run(): void
    {
        $moas = [
            'cash',
            'Check',
            'Guarantee Letter'
        ];

        foreach ($moas as $moa) {
            ModeOfAssistance::firstOrCreate([
                'name'  => $moa,
                'remarks'   => 'seeder generated',
            ]);
        }
    }
}