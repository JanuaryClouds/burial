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

        if ($lastStagePosition === null) {
            dump('No workflow stages found. Skipping WorkflowHistorySeeder.');
            return;
        }
        $undoableStageUuids = [
            $workflowStages->where('name', 'Budget')->first()->uuid,
            $workflowStages->where('name', 'Accounting')->first()->uuid,
            $workflowStages->where('name', 'Treasury')->first()->uuid,
            $workflowStages->where('name', 'Releasing')->first()->uuid,
            $workflowStages->where('name', 'Closing')->first()->uuid,
        ]; // prevent reverting back to recommendation stage

        $firstStage = WorkflowStage::where('position', 1)->first();

        foreach ($applications as $application) {
            while (true) {
                $updatedApplication = $application->fresh();

                $currentStage = $updatedApplication->workflowStage;

                // Stop if the application has already reached the last stage
                if ($currentStage != null && $currentStage->position >= $lastStagePosition) {
                    break;
                }

                dump('[INFO]: Seeding ' . $application->tracking_no . ' for ' . $application->currentRecommendation()->funeralAssistanceType?->name);

                $fromStage = $updatedApplication->fromStage();
                $toStage = $updatedApplication->toStage();
                $previousHistory = $updatedApplication->previousHistory();

                dump('[INFO][' . $application->tracking_no . ']: From Stage: ' . $fromStage?->name);
                dump('[INFO][' . $application->tracking_no . ']: To Stage: ' . $toStage?->name);
                dump('[INFO][' . $application->tracking_no . ']: Previous History: ' . $previousHistory?->uuid);

                $chances = rand(1, 100);
                dump('[INFO][' . $application->tracking_no . ']: Chance: ' . $chances);
    
                if ($chances <= 80) {
                    $this->toNextStage(
                        $updatedApplication,
                        $fromStage,
                        $toStage,
                        $previousHistory,
                    );
                    
                    if (rand(0,9) === 9) {
                        dump('[INFO][' . $application->tracking_no . ']: Paused for manual testing.');
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
                        $base = $previousHistory
                            ? Carbon::parse($previousHistory->date_out)
                            : Carbon::parse($application->currentRecommendation()->created_at ?? $application->created_at);

                        $dateIn = Carbon::parse($base)->addMinutes(5);
                        $dateOut = Carbon::parse($dateIn)->addMinutes(5);

                        $this->createWorkflowHistory(
                            $application->currentRecommendation(),
                            $currentStage,
                            null,
                            $dateIn,
                            $dateOut,
                            'Client has cancelled their application'
                        );

                        $updatedApplication->currentRecommendation()->update([
                            'status' => 'cancelled',
                        ]);

                        dump('[INFO][' . $application->tracking_no . ']: Cancelled.');
                        break;
                    } elseif ($chances >= 96 && $chances <= 100) {
                        // Referral
                        $base = $previousHistory
                            ? Carbon::parse($previousHistory->date_out)
                            : Carbon::parse($application->currentRecommendation()->created_at ?? $application->created_at);

                        $dateIn = Carbon::parse($base)->addMinutes(5);
                        $dateOut = Carbon::parse($dateIn)->addMinutes(5);

                        $this->createWorkflowHistory(
                            $application->currentRecommendation(),
                            $currentStage,
                            null,
                            $dateIn,
                            $dateOut,
                            'Client has been referred'
                        );

                        $updatedApplication->currentRecommendation()->update([
                            'status' => 'rejected'
                        ]);
        
                        dump('[INFO][' . $application->tracking_no . ']: Referred.');
                        break;
                    }
                }
            }
        }
    }

    /**
     * Summary of toNextStage
     * @param Application $application
     * @param WorkflowStage $fromStage
     * @param WorkflowStage $toStage
     * @param WorkflowHistory $previousHistory
     * @return void
     */
    public function toNextStage(
        Application $application, 
        ?WorkflowStage $fromStage,
        ?WorkflowStage $toStage,
        ?WorkflowHistory $previousHistory,
    ): void {
        // Update that recommendation to approved
        if ($application->currentRecommendation()->status == 'pending') {
            $application->currentRecommendation()->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);
        }
        
        $base = $previousHistory
            ? Carbon::parse($previousHistory->date_out)
            : Carbon::parse($application->currentRecommendation()->created_at);
            
        $dateIn = Carbon::parse($base)->addMinutes(5);
        $dateOut = Carbon::parse($dateIn)->addMinutes(5);

        if ($previousHistory != null) {
            dump('[INFO][' . $application->tracking_no . ']: Previous Date Out: ' . $previousHistory->date_out);
            dump('[INFO][' . $application->tracking_no . ']: Date In: ' . $dateIn);
        }
            
        $application->update([
            'current_workflow_stage_uuid' => $toStage?->uuid,
        ]);

        $this->createWorkflowHistory(
            $application->currentRecommendation(),
            $fromStage,
            $toStage,
            $dateIn,
            $dateOut,
        );

        dump('[SUCCESS][' . $application->tracking_no . ']: Seeded');
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
        $base = $previousHistory
            ? Carbon::parse($previousHistory->date_out)
            : Carbon::parse($application->currentRecommendation()->created_at);

        $dateIn = Carbon::parse($base)->addMinutes(5);
        $dateOut = Carbon::parse($dateIn)->addMinutes(5);
        
        if ($previousHistory != null) {
            dump('[INFO][' . $application->tracking_no . ']: Previous Date Out: ' . $previousHistory->date_out);
            dump('[INFO][' . $application->tracking_no . ']: Date In: ' . $dateIn);
        }
        
        $application->update([
            'current_workflow_stage_uuid' => null,
        ]);

        // Create workflowHistory using the latest as the previous stage
        $this->createWorkflowHistory(
            $application->currentRecommendation(),
            $currentStage,
            null,
            $dateIn,
            $dateOut,
            'Client has been referred to another department'
        );

        // Update that recommendation to rejected
        $application->currentRecommendation()->update([
            'status' => 'rejected',
        ]);
        
        // Create a recommendation model
        Recommendation::factory()->create([
            'application_uuid' => $application->uuid,
            'recommended_by' => User::whereHas('roles', function($query) {
                $query->where('name', 'staff');
            })->inRandomOrder()->first()->id,
            'created_at' => $dateOut,
        ]);

        dump('[INFO][' . $application->tracking_no . ']: Returned application to recommendation stage');
    }

    /**
     * Summary of createWorkflowHistory
     * @param Recommendation $recommendation
     * @param WorkflowStage $fromStage
     * @param WorkflowStage $toStage
     * @param string|null $reason
     * @return void
     */
    public function createWorkflowHistory(
        Recommendation $recommendation,
        ?WorkflowStage $fromStage,
        ?WorkflowStage $toStage,
        Carbon $dateIn,
        Carbon $dateOut,
        ?string $reason = null,
    ): void {
        $workflowHistory = WorkflowHistory::factory()->create([
            'recommendation_uuid' => $recommendation->uuid,
            'date_in' => $dateIn,
            'date_out' => $dateOut,
            'from_stage_uuid' => $fromStage?->uuid,
            'to_stage_uuid' => $toStage?->uuid,
            'reason' => $reason,
        ]);

        dump('  [SUCCESS][' . $recommendation->application->tracking_no . ']: UUID: ' . $workflowHistory->uuid . ' | Recommendation : ' . $recommendation->uuid . ' | ' . ' Date In: ' . $dateIn . ' Date Out: ' . $dateOut);
    }
}
