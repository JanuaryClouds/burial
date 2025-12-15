<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;

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
                'remarks' => 'seeder generated',
            ]);
        }
    }
}
