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
use App\Traits\HasWorkflowHistory;
use App\Traits\HasWorkHours;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class InterviewSeeder extends Seeder
{
    use HasWorkflowHistory;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::whereHas('application')
            ->get();

        foreach ($clients as $client) {
            if (rand(0, 9) >= 2) {
                $created_at = Carbon::generateRandomDateTime(
                    Carbon::parse($client->application->created_at),
                    Carbon::parse($client->application->created_at)->addMinutes(rand(5, 60)),
                );

                $schedule = Carbon::generateRandomDateTime(
                    $created_at,
                    Carbon::parse($created_at)->addMinutes(rand(5, 60)),
                );

                $interview = Interview::factory()->create([
                    'client_uuid' => $client->uuid,
                    'status' => 'done',
                    'schedule' => $schedule,
                    'created_at' => $created_at,
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
