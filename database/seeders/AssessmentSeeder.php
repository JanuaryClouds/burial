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
use App\Traits\HasWorkflowHistory;
use App\Traits\HasWorkHours;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AssessmentSeeder extends Seeder
{
    use HasWorkHours, HasWorkflowHistory;

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
                    'created_at' => Carbon::generateRandomDateTime(
                        Carbon::parse($client->interviews()->latest()->first()->created_at), 
                        Carbon::parse($client->interviews()->latest()->first()->created_at)->addMinutes(rand(5, 60)),
                    ),
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
