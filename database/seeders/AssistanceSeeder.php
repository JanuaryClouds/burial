<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Assistance;

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

        foreach($assistances as $assistance)
        {
            Assistance::firstOrCreate(['name' => $assistance]);
        }
    }
}