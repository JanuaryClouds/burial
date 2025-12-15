<?php

namespace Database\Seeders;

use App\Models\BurialAssistance;
use Illuminate\Database\Seeder;

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
