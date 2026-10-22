<?php

namespace Database\Seeders;

use App\Models\FuneralAssistanceType;
use App\Models\Workflow;
use Illuminate\Database\Seeder;

class FuneralAssistanceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Burial Assistance' => 'Incentives to be given to families of the deceased to assist in the burial process of the beneficiary',
            'Libreng Libing' => 'Free internment for certified indigent of Taguig City in public city cemeteries',
            'Mortuary' => 'Aid the beneficiary\'s morgue process',
            'Exhumation' => 'Assistance for the transfer of remains',
        ];

        $primaryWorkflow = Workflow::firstWhere('name', 'Funeral Assistance');

        foreach ($types as $name => $description) {
            FuneralAssistanceType::create([
                'name' => $name,
                'description' => $description,
                'workflow_uuid' => $primaryWorkflow->uuid,
            ]);
        }
    }
}
