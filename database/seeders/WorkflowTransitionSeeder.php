<?php

namespace Database\Seeders;

use App\Models\Workflow;
use App\Models\WorkflowStage;
use App\Models\WorkflowTransition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class WorkflowTransitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = WorkflowStageSeeder::stages();

        $primaryWorkflow = Workflow::where('name', 'Funeral Assistance')->first();

        for ($i = 0; $i < count($stages); $i++) {
            $fromStage = WorkflowStage::firstOrCreate([
                'workflow_uuid' => $primaryWorkflow->uuid,
                'name' => $stages[$i]['name'],
                'description' => $stages[$i]['description'],
            ]);

            if ($i + 1 < count($stages)) {
                $toStage = WorkflowStage::firstOrCreate([
                    'workflow_uuid' => $primaryWorkflow->uuid,
                    'name' => $stages[$i + 1]['name'],
                    'description' => $stages[$i + 1]['description'],
                ]);

                $permission = Permission::firstOrCreate([
                    'name' => $stages[$i]['permission'],
                ]);

                WorkflowTransition::create([
                    'workflow_uuid' => $primaryWorkflow->uuid,
                    'from_stage_uuid' => $fromStage->uuid,
                    'to_stage_uuid' => $toStage->uuid,
                    'permission_id' => $permission->id,
                ]);
            }
        }
    }
}
