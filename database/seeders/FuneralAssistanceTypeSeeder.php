<?php

namespace Database\Seeders;

use App\Models\FuneralAssistanceType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FuneralAssistanceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Burial Assistance' => 'Financial assistance to support a beneificary\' burial',
            'Libreng Libing' => 'Free internment for certified indigent of Taguig City in public city cemeteries',
            'Mortuary' => 'Aid the beneficiary\'s morgue process',
            'Exhumation' => 'Assistance for the transfer of remains',
        ];

        foreach ($types as $name => $description) {
            FuneralAssistanceType::create([
                'name' => $name,
                'description' => $description,
            ]);
        }
    }
}
