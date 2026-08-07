<?php

namespace Database\Seeders;

use App\Models\FuneralAssistanceType;
use App\Models\Workflow;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workflow = Workflow::create([
            'name' => 'Funeral Assistance',
            'description' => 'Funeral Assistance budgeted by the Taguig City Local Government',
        ]);

        $funeralAssistanceTypes = FuneralAssistanceType::all();
        foreach ($funeralAssistanceTypes as $type) {
            $type->update([
                'workflow_uuid' => $workflow->uuid,
            ]);
        }
    }
}
