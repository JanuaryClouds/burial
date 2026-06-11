<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientRecommendation;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientRecommendationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::whereHas('assessment')
            ->whereDoesntHave('referral')
            ->get();

        foreach ($clients as $client) {
            if (rand(0, 1) == 1) {
                $random_type = rand(0, 1);
                $recommendation_type = '';

                switch ($random_type) {
                    case 0:
                        $recommendation_type = 'burial';
                        break;
                    case 1:
                        $recommendation_type = 'libreng_libing';
                        break;
                    default:
                        break;
                }

                $recommendation = ClientRecommendation::factory()->create([
                    'client_id' => $client->id,
                    'type' => $recommendation_type,
                ]);

                Notification::factory()->create([
                    'notifiable_id' => $client->user->id,
                    'notifiable_type' => User::class,
                    'source_type' => ClientRecommendation::class,
                    'source_id' => $recommendation->id,
                    'payload' => Notification::defaultPayload(ClientRecommendation::class),
                ]);
            }
        }
    }
}
