<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Recommendation;
use App\Models\User;
use App\Models\WorkflowHistory;
use App\Models\WorkflowStage;
use App\Models\WorkflowTransition;
use App\Traits\HasWorkflowHistory;
use App\Traits\HasWorkHours;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class WorkflowHistorySeeder extends Seeder
{
    use HasWorkHours, HasWorkflowHistory;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applications = Application::whereHas('client.interviews')
            ->whereHas('recommendations', function ($query) {
                $query->whereIn('status', ['pending']);
            })
            ->orderBy('tracking_no', 'asc')
            ->get();

        $workflowStages = WorkflowStage::all();
        $lastStagePosition = WorkflowStage::max('position');
        $undoableStageUuids = [
            $workflowStages->where('name', 'Budget')->first()->uuid,
            $workflowStages->where('name', 'Accounting')->first()->uuid,
            $workflowStages->where('name', 'Treasury')->first()->uuid,
            $workflowStages->where('name', 'Releasing')->first()->uuid,
            $workflowStages->where('name', 'Closing')->first()->uuid,
        ]; // prevent reverting back to recommendation stage

        foreach ($applications as $application) {
            $initialStagePosition = $application->workflowStage?->position;

            if ($initialStagePosition == null) {
                $initialStagePosition = 0;
            }
            
            while ($initialStagePosition !== $lastStagePosition) {
                $updatedApplication = $application->fresh();

                $currentStage = $updatedApplication->workflowStage;

                if ($currentStage && $currentStage->position > $lastStagePosition) {
                    break;
                }

                $previousStage = $updatedApplication->previousStage();
                $nextStage = $updatedApplication->nextStage();
                $previousHistory = $updatedApplication->previousHistory();
    
                $chances = rand(1, 100);
                dump('Tracking No: ' . $updatedApplication->tracking_no . ' | Current Stage: ' . $currentStage?->name . ' | Chance: ' . $chances);
    
                if ($chances <= 80) {
                    $this->toNextStage(
                        $updatedApplication,
                        $previousStage,
                        $nextStage,
                        $previousHistory,
                    );
                    
                    if (rand(0,9) === 9) {
                        dump('Paused ' . $updatedApplication->tracking_no . ' for manual testing.');
                        break;
                    }
                } else {
                    if (in_array($updatedApplication->current_workflow_stage_uuid, $undoableStageUuids)) continue;
                    
                    if ($chances >= 81 && $chances <= 90) {
                        $this->revertToRecommendation(
                            $updatedApplication,
                            $previousHistory,
                            $currentStage
                        );
                    } elseif ($chances >= 91 && $chances <= 95) {
                        // Chanced to be canceled
                        $updatedApplication->currentRecommendation()->update([
                            'status' => 'canceled',
                        ]);

                        dump('Canceled ' . $updatedApplication->tracking_no);
                        break;
                    } elseif ($chances >= 96 && $chances <= 100) {
                        // Referral
                        $updatedApplication->currentRecommendation()->update([
                            'status' => 'rejected'
                        ]);
        
                        dump($updatedApplication->tracking_no . ' has been referred.');
                        break;
                    }
                }
            }
        }
    }

    /**
     * Summary of toNextStage
     * @param Application $application
     * @param WorkflowStage $previousStage
     * @param WorkflowStage $nextStage
     * @param WorkflowHistory $previousHistory
     * @return void
     */
    public function toNextStage(
        Application $application, 
        ?WorkflowStage $previousStage,
        ?WorkflowStage $nextStage,
        ?WorkflowHistory $previousHistory,
    ): void {
        dump('Next stage to seed: ' . $nextStage?->name);

        // Update that recommendation to approved
        if ($application->currentRecommendation()->status == 'pending') {
            $application->currentRecommendation()->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);
        }

        $dateIn = $previousHistory ? Carbon::generateRandomDateTime(
            Carbon::parse($previousHistory->date_out),
            Carbon::parse($previousHistory->date_out)->addMinutes(rand(5, 20))
        ) : Carbon::generateRandomDateTime(
            Carbon::parse($application->currentRecommendation()->created_at),
            Carbon::parse($application->currentRecommendation()->created_at)->addMinutes(rand(5, 20))
        );

        $dateOut = Carbon::generateRandomDateTime(
            Carbon::parse($dateIn),
            Carbon::parse($dateIn)->addMinutes(rand(5, 20))
        );

        $application->update([
            'current_workflow_stage_uuid' => $nextStage?->uuid,
        ]);

        $this->createWorkflowHistory(
            $application,
            $previousStage,
            $nextStage,
            $dateIn,
            $dateOut,
        );

        dump('Seeded ' . $application->tracking_no . ' with the next stage of ' . $application->fresh()->workflowStage?->name);
    }

    /**
     * Summary of revertToRecommendation
     * @param Application $application
     * @param WorkflowHistory $previousHistory
     * @param WorkflowStage $currentStage
     * @return void
     */
    public function revertToRecommendation(
        Application $application,
        ?WorkflowHistory $previousHistory,
        ?WorkflowStage $currentStage
    ): void {
        // Add a chance for the recommendation to be rejected and set the next stage to be the recommendation stage
        
        $dateIn = $previousHistory ? 
            Carbon::generateRandomDateTime(
                Carbon::parse($previousHistory->date_out),
                    Carbon::parse($previousHistory->date_out)->addMinutes(rand(5, 20))
                ) : Carbon::generateRandomDateTime(
                    Carbon::parse($application->currentRecommendation()->created_at),
                    Carbon::parse($application->currentRecommendation()->created_at)->addMinutes(rand(5, 20))
                );

        // Update that recommendation to rejected
        $application->currentRecommendation()->update([
            'status' => 'rejected',
        ]);
            
        $dateOut = Carbon::generateRandomDateTime(
            Carbon::parse($dateIn),
            Carbon::parse($dateIn)->addMinutes(rand(5, 20))
        );

        $application->update([
            'current_workflow_stage_uuid' => null,
        ]);

        // Create workflowHistory using the latest as the previous stage
        $this->createWorkflowHistory(
            $application,
            $currentStage,
            null,
            $dateIn,
            $dateOut,
        );
        
        // Create a recommendation model
        RecommendationSeeder::seed($application);

        dump('Returned application ' . $application->tracking_no . ' to recommendation stage');
    }

    /**
     * Summary of createWorkflowHistory
     * @param Application $application
     * @param WorkflowStage $currentStage
     * @param WorkflowStage $nextStage
     * @return void
     */
    public function createWorkflowHistory(
        Application $application,
        ?WorkflowStage $currentStage,
        ?WorkflowStage $nextStage,
        Carbon $dateIn,
        Carbon $dateOut,
    ): void {
        WorkflowHistory::factory()->create([
            'application_uuid' => $application->uuid,
            'date_in' => $dateIn,
            'date_out' => $dateOut,
            'from_stage_uuid' => $currentStage?->uuid,
            'to_stage_uuid' => $nextStage?->uuid,
            'created_at' => $dateIn,
            'updated_at' => $dateOut,
        ]);
    }
}
