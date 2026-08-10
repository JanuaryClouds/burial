<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Client;
use App\Models\Notification;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowHistory;
use App\Models\WorkflowStage;
use App\Models\WorkflowTransition;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::whereHas('interviews')->get();

        dump($clients->count().' Clients with Interviews to Seed');

        foreach ($clients as $client) {
            if (rand(0, 9) >= 2 && $client->application) {
                $assessment = Assessment::factory()->create([
                    'application_uuid' => $client->application->uuid,
                ]);

                $previousStage = $client->application->workflowHistory()->latest()->first();
                $nextStage = WorkflowTransition::where('from_stage_uuid', $previousStage->to_stage_uuid)->first();
                WorkflowHistory::factory()->create([
                    'from_stage_uuid' => $previousStage->to_stage_uuid,
                    'to_stage_uuid' => $nextStage->to_stage_uuid,
                    'application_uuid' => $client->application->uuid
                ]);

                $client->application->update([
                    'current_workflow_stage_uuid' => $nextStage->to_stage_uuid,
                ]);

                Notification::factory()->create([
                    'notifiable_id' => $client->user_id,
                    'notifiable_type' => User::class,
                    'source_type' => Assessment::class,
                    'source_id' => $assessment->id,
                    'payload' => Notification::defaultPayload(Assessment::class),
                ]);
            }
        }

        dump(Assessment::count().' assessments have been provided.');
    }
}
