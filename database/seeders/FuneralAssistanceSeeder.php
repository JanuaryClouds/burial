<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\FuneralAssistance;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class FuneralAssistanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::whereHas('recommendation', function ($query) {
            $query->where('type', 'libreng_libing');
        })
            ->get();

        foreach ($clients as $client) {
            $funeralAssistance = FuneralAssistance::factory()->create([
                'client_id' => $client->id,
                'remarks' => $client->recommendation->first()->remarks,
            ]);

            Notification::factory()->create([
                'notifiable_id' => $client->user->id,
                'notifiable_type' => User::class,
                'source_type' => FuneralAssistance::class,
                'source_id' => $funeralAssistance->id,
                'payload' => Notification::defaultPayload(FuneralAssistance::class),
            ]);
        }
    }
}
