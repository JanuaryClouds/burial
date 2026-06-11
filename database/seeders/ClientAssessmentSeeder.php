<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientAssessment;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientAssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::whereHas('interviews')->get();

        foreach ($clients as $client) {
            $assessment = ClientAssessment::factory()->create([
                'client_id' => $client->id,
            ]);

            Notification::factory()->create([
                'notifiable_id' => $client->user->id,
                'notifiable_type' => User::class,
                'source_type' => ClientAssessment::class,
                'source_id' => $assessment->id,
                'payload' => Notification::defaultPayload(ClientAssessment::class),
            ]);
        }
    }
}
