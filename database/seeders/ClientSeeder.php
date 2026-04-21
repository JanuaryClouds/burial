<?php

namespace Database\Seeders;

use App\Models\BurialAssistance;
use App\Models\Claimant;
use App\Models\Client;
use App\Models\ClientAssessment;
use App\Models\ClientRecommendation;
use App\Models\FuneralAssistance;
use App\Models\Interview;
use App\Models\Notification;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Database\Seeder;
use Str;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory()->count(10)->create([
            'citizen_id' => fn () => Str::uuid()->toString(),
        ]);

        $clients = Client::factory()->count(10)->create([
            'user_id' => fn () => $users->random()->id,
        ]);

        foreach ($clients as $client) {
            if (! $client) {
                continue;
            }

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

                $random_assistance = rand(0, 2);
                switch ($random_assistance) {
                    case 0:
                        // Referral
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
                        break;
                    case 1:
                        // Burial Assistance
                        $recommendation = ClientRecommendation::factory()->create([
                            'client_id' => $client->id,
                            'type' => 'funeral',
                        ]);

                        Notification::factory()->create([
                            'notifiable_id' => $client->user->id,
                            'notifiable_type' => User::class,
                            'source_type' => ClientRecommendation::class,
                            'source_id' => $recommendation->id,
                            'payload' => Notification::defaultPayload(ClientRecommendation::class),
                        ]);

                        $burial_assistance = BurialAssistance::factory()->create([
                            'application_date' => $client->created_at,
                            'swa' => $assessment->assessment,
                            'encoder' => $users->random()->id,
                            'amount' => $recommendation->amount,
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
                            'contact_number' => $client->user?->contact_number,
                            'relationship_id' => $client->socialInfo?->relationship?->id,
                        ]);

                        Notification::factory()->create([
                            'notifiable_id' => $client->user->id,
                            'notifiable_type' => User::class,
                            'source_type' => BurialAssistance::class,
                            'source_id' => $burial_assistance->id,
                            'payload' => Notification::defaultPayload(BurialAssistance::class),
                        ]);
                        
                        break;
                    case 2:
                        // Funeral Assistance
                        $recommendation = ClientRecommendation::factory()->create([
                            'client_id' => $client->id,
                            'referral' => 'Taguig City Public Cemetery',
                            'amount' => null,
                            'moa_id' => null,
                        ]);

                        Notification::factory()->create([
                            'notifiable_id' => $client->user->id,
                            'notifiable_type' => User::class,
                            'source_type' => ClientRecommendation::class,
                            'source_id' => $recommendation->id,
                            'payload' => Notification::defaultPayload(ClientRecommendation::class),
                        ]);

                        $funeralAssistance = FuneralAssistance::factory()->create([
                            'client_id' => $client->id,
                            'remarks' => $recommendation->remarks,
                        ]);

                        Notification::factory()->create([
                            'notifiable_id' => $client->user->id,
                            'notifiable_type' => User::class,
                            'source_type' => FuneralAssistance::class,
                            'source_id' => $funeralAssistance->id,
                            'payload' => Notification::defaultPayload(FuneralAssistance::class),
                        ]);

                        break;
                    default:
                        // Referral
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
}
