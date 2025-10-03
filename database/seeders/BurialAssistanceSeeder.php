<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Deceased;
use App\Models\Claimant;
use App\Models\BurialAssistance;

class BurialAssistanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BurialAssistance::factory()->count(10)->create();
    }
}
