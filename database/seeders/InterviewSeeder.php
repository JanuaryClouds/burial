<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Interview;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class InterviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::all();

        foreach ($clients as $client) {
            if (rand(0, 1) == 1) {
                $interview = Interview::factory()->create([
                    'client_id' => $client->id,
                    'status' => 'done',
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
    }
}
