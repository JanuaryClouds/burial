<?php

namespace Database\Seeders;

use App\Models\ModeOfAssistance;
use Illuminate\Database\Seeder;

class MoaSeeder extends Seeder
{
    public function run(): void
    {
        $moas = [
            'Cash',
            'Check',
            'Guarantee Letter',
        ];

        foreach ($moas as $moa) {
            ModeOfAssistance::firstOrCreate([
                'name' => $moa,
                'remarks' => 'seeder generated',
            ]);
        }
    }
}
