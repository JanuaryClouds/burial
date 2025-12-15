<?php

namespace Database\Seeders;

use App\Models\Assistance;
use Illuminate\Database\Seeder;

class AssistanceSeeder extends Seeder
{
    public function run(): void
    {
        $assistances = [
            'Counseling',
            'Financial Assistance',
            'Legal Assitance',
            'Food Subsidy',
            'Livelihood',
            'Educational',
            'Medical',
            'Burial',
            'Transportation',
            'Material',
            'Food pack',
            'Clothes',
            'Meal',
            'Other',
        ];

        foreach ($assistances as $assistance) {
            Assistance::firstOrCreate(['name' => $assistance]);
        }
    }
}
