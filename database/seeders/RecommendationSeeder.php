<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\FuneralAssistanceType;
use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Database\Seeder;

class RecommendationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applications = Application::whereHas('assessment')->get();
        $staff = User::whereHas('roles')->get();

        dump('Number of Applications to Seed: '.$applications->count());

        foreach ($applications as $application) {
            if (rand(0, 1) === 1) {
                $recommendation = Recommendation::factory()->create([
                    'application_uuid' => $application->uuid,
                    'recommended_by' => $staff->random(1)->first()->id ?? 1,
                ]);

                switch (rand(0, 3)) {
                    case 1:
                        $recommendation->update([
                            'status' => 'approved',
                            'approved_at' => now()->subDays(rand(1, 10)),
                        ]);
                        break;
                    case 2:
                        $recommendation->update([
                            'status' => 'rejected',
                        ]);
                        break;
                    case 3:
                        $recommendation->update([
                            'status' => 'canceled',
                        ]);
                        break;
                    default:
                        break;
                }
            }
        }
    }
}
