<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\District;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $District = [
            '1',
            '2',
        ];

        foreach ($District as $district) {
            District::firstOrCreate([
                'name' => $district,
                'remarks'   => 'seeder generated',
            ]);
        }
    }
}