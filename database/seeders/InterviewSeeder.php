<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Interview;
use App\Models\Notification;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowHistory;
use App\Models\WorkflowStage;
use App\Models\WorkflowTransition;
use Illuminate\Database\Seeder;

class InterviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::whereHas('application')
            ->get();

        foreach ($clients as $client) {
            if (rand(0, 1) == 1) {
                $interview = Interview::factory()->create([
                    'client_uuid' => $client->uuid,
                    'status' => 'done',
                ]);

                $workflow = Workflow::where('name', 'Funeral Assistance')->first();
                $interviewStage = WorkflowStage::where('name', 'Interview')->first();
                $nextStage = WorkflowTransition::where('from_stage_uuid', $interviewStage->uuid)->first();
                WorkflowHistory::factory()->create([
                    'from_stage_uuid' => $interviewStage->uuid,
                    'to_stage_uuid' => $nextStage->to_stage_uuid,
                    'application_uuid' => $client->application->uuid
                ]);

                Notification::factory()->create([
                    'notifiable_id' => $client->user->id,
                    'notifiable_type' => User::class,
                    'source_type' => Interview::class,
                    'source_id' => $interview->id,
                    'payload' => Notification::defaultPayload(Interview::class),
                ]);
            }
        }

        dump(Interview::count().' Interviews Seeded');
    }
}
