<?php

namespace Database\Seeders;

use App\Models\BurialAssistance;
use App\Models\Claimant;
use App\Models\Client;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class BurialAssistanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'staff');
        })->get();

        $clients = Client::whereHas('recommendation', function ($query) {
            $query->where('type', 'burial');
        })->get();

        foreach ($clients as $client) {
            $burial_assistance = BurialAssistance::factory()->create([
                'application_date' => $client->created_at,
                'swa' => $client->assessment->first()->assessment,
                'encoder' => $users->random()->id,
                'amount' => $client->recommendation->first()->amount,
                'initial_checker' => $users->random()->id,
            ]);

            Claimant::factory()->create([
                'client_id' => $client->id,
                'burial_assistance_id' => $burial_assistance->id,
                'first_name' => $client->user?->first_name,
                'middle_name' => $client->user?->middle_name ?? null,
                'last_name' => $client->user?->last_name,
                'suffix' => $client->user?->suffix ?? null,
                'date_of_birth' => $client->date_of_birth,
                'address' => $client->house_no.' '.$client->street,
                'barangay_id' => $client->barangay_id,
                'contact_number' => $client->user?->contact_number ?? 'N/A',
                'relationship_id' => $client->socialInfo?->relationship?->id,
            ]);

            Notification::factory()->create([
                'notifiable_id' => $client->user->id,
                'notifiable_type' => User::class,
                'source_type' => BurialAssistance::class,
                'source_id' => $burial_assistance->id,
                'payload' => Notification::defaultPayload(BurialAssistance::class),
            ]);
        }
    }
}
