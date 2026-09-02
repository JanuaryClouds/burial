<?php

namespace Database\Seeders;

use App\Models\Application;
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
        $applications = Application::whereHas('assessment')
            ->whereHas('recommendations', function ($query) {
                $query->whereIn('status', ['referred']);
            })
            ->get();

        foreach ($applications as $application) {
            if (rand(0, 1) === 0) {
                $referral = Referral::factory()->create([
                    'application_uuid' => $application->uuid,
                ]);

                Notification::factory()->create([
                    'notifiable_id' => $application->client->user_id,
                    'notifiable_type' => User::class,
                    'source_type' => Referral::class,
                    'source_id' => $referral->id,
                    'payload' => Notification::defaultPayload(Referral::class),
                ]);
            }
        }

        dump(Referral::count().' referrals have been provided.');
    }
}
