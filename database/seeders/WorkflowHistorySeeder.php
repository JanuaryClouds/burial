<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Recommendation;
use App\Models\User;
use App\Models\WorkflowHistory;
use App\Models\WorkflowStage;
use App\Models\WorkflowTransition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkflowHistorySeeder extends Seeder
{
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
        $workflowTransitions = WorkflowTransition::all();
        $recommendationStage = $workflowStages->where('name', 'Recommendation')->first();
        $undoableStageUuids = [
            $workflowStages->where('name', 'Budget')->first()->uuid,
            $workflowStages->where('name', 'Accounting')->first()->uuid,
            $workflowStages->where('name', 'Treasury')->first()->uuid,
            $workflowStages->where('name', 'Releasing')->first()->uuid,
            $workflowStages->where('name', 'Closing')->first()->uuid,
        ]; // prevent reverting back to recommendation stage
        $staff = User::whereHas('roles')->get();

        foreach ($applications as $application) {
            do {
                $chances = rand(1, 100);
                dump($application->tracking_no . ' chance: ' . $chances);
    
                if ($chances <= 80) {
                    // Update that recommendation to approved
                    if ($application->recommendations()->latest()->first()->status == 'pending') {
                        $application->recommendations()->latest()->first()->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                        ]);
                    }
                    
                    // Create workflowHistory using the latest as the previous stage
                    $currentStageUuid = $application->current_workflow_stage_uuid;
                    $nextStage = $workflowTransitions->where('from_stage_uuid', $currentStageUuid)
                        ->first();
    
                    WorkflowHistory::factory()->create([
                        'application_uuid' => $application->uuid,
                        'from_stage_uuid' => $currentStageUuid,
                        'to_stage_uuid' => $nextStage->to_stage_uuid,
                    ]);
    
                    $application->update([
                        'current_workflow_stage_uuid' => $nextStage->to_stage_uuid,
                    ]);
    
                    dump('Seeded ' . $application->tracking_no . ' with the next stage of ' . $nextStage->toStage->name);
                    
                    if (rand(0,9) === 9) {
                        dump('Paused ' . $application->tracking_no . ' for manual testing.');
                        break;
                    }
                } else {
                    if (in_array($application->current_workflow_stage_uuid, $undoableStageUuids)) continue;
                    
                    if ($chances >= 81 && $chances <= 90) {
                        // Add a chance for the recommendation to be rejected and set the next stage to be the recommendation stage
                        // Update that recommendation to rejected
                        $application->recommendations()->latest()->first()->update([
                            'status' => 'rejected',
                        ]);
                        
                        // Create workflowHistory using the latest as the previous stage
                        WorkflowHistory::factory()->create([
                            'application_uuid' => $application->uuid,
                            'from_stage_uuid' => $currentStageUuid,
                            'to_stage_uuid' => $recommendationStage->uuid,
                        ]);
        
                        $application->update([
                            'current_workflow_stage_uuid' => $recommendationStage->uuid,
                        ]);
                        
                        // Create a recommendation model
                        Recommendation::factory()->create([
                            'application_uuid' => $application->uuid,
                            'recommended_by' => $staff->random(1)->first()->id ?? 1,
                        ]);
        
                        dump('Returned application ' . $application->tracking_no . ' to recommendation stage');
                    } elseif ($chances >= 91 && $chances <= 95) {
                        // Chanced to be canceled
                        $application->recommendations()->latest()->first()->update([
                            'status' => 'canceled',
                        ]);
        
                        dump('Canceled ' . $application->tracking_no);
                        break;
                    } elseif ($chances >= 96 && $chances <= 100) {
                        // Referral
                        $application->recommendations()->latest()->first()->update([
                            'status' => 'rejected'
                        ]);
        
                        dump($application->tracking_no . ' has been referred.');
                        break;
                    }
                }
            } while ($workflowTransitions->where('from_stage_uuid', $application->current_workflow_stage_uuid)->first() !== null);
        }
    }
}
