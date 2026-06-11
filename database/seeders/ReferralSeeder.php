<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Notification;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReferralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::whereHas('assessment')
            ->where(function ($query) {
                $query->whereDoesntHave('recommendation');
            })
            ->get();

        foreach ($clients as $client) {
            if (rand(0, 2) === 0) {
                $referral = Referral::factory()->create([
                    'client_id' => $client->id,
                ]);

                Notification::factory()->create([
                    'notifiable_id' => $client->user->id,
                    'notifiable_type' => User::class,
                    'source_type' => Referral::class,
                    'source_id' => $referral->id,
                    'payload' => Notification::defaultPayload(Referral::class),
                ]);
            }
        }
    }
}
