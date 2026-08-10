<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\FuneralAssistanceType;
use App\Models\Recommendation;
use App\Models\User;
use App\Models\WorkflowHistory;
use App\Models\WorkflowTransition;
use Illuminate\Database\Seeder;

class RecommendationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applications = Application::whereHas('assessment')->get();
        $staff = User::whereHas('roles')->get();

        dump('Number of Applications to Seed: '.$applications->count());

        foreach ($applications as $application) {
            if (rand(0, 9) >= 2) {
                $recommendation = Recommendation::factory()->create([
                    'application_uuid' => $application->uuid,
                    'recommended_by' => $staff->random(1)->first()->id ?? 1,
                ]);

                $previousStage = $application->workflowHistory()->latest()->first();
                $nextStage = WorkflowTransition::where('from_stage_uuid', $previousStage->to_stage_uuid)->first();
                WorkflowHistory::factory()->create([
                    'from_stage_uuid' => $previousStage->to_stage_uuid,
                    'to_stage_uuid' => $nextStage->to_stage_uuid,
                    'application_uuid' => $application->uuid
                ]);

                $application->update([
                    'current_workflow_stage_uuid' => $nextStage->to_stage_uuid,
                ]);
            }
        }
    }
}
