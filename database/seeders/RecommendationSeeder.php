<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\FuneralAssistanceType;
use App\Models\Recommendation;
use App\Models\User;
use App\Models\WorkflowHistory;
use App\Models\WorkflowStage;
use App\Models\WorkflowTransition;
use App\Traits\HasWorkflowHistory;
use App\Traits\HasWorkHours;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class RecommendationSeeder extends Seeder
{
    use HasWorkHours, HasWorkflowHistory;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applications = Application::whereHas('assessment')->get();
        dump('Number of Applications to Seed: '.$applications->count());

        foreach ($applications as $application) {
            if (rand(0, 9) >= 2) {
                $this->seed($application);
            }
        }
    }

    public static function seed(Application $application)
    {
        $staff = User::whereHas('roles')->get();

        $recommendation = Recommendation::factory()->create([
            'application_uuid' => $application->uuid,
            'recommended_by' => $staff->random(1)->first()->id ?? 1,
            'created_at' => Carbon::generateRandomDateTime(
                Carbon::parse($application->assessment->created_at),
                Carbon::parse($application->assessment->created_at)->addMinutes(rand(5, 10))
            ),
        ]);

        $workflow = $recommendation->funeralAssistanceType->workflow;
        $firstStage = $workflow->stages()->where('position', 1)->first();

        $dateIn = $recommendation->created_at;
        $dateOut = $dateIn->addMinutes(rand(1, 10));

        WorkflowHistory::factory()->create([
            'application_uuid' => $application->uuid,
            'from_stage_uuid' => null,
            'to_stage_uuid' => $firstStage->uuid,
            'date_in' => $dateIn,
            'date_out' => $dateOut,
            'created_at' => $dateIn,
            'updated_at' => $dateOut,
        ]);

        $application->update([
            'current_workflow_stage_uuid' => null,
        ]);
    }
}
