<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Recommendation;
use App\Models\Remark;
use App\Models\User;
use App\Traits\HasWorkflowHistory;
use Illuminate\Database\Seeder;

class RecommendationSeeder extends Seeder
{
    use HasWorkflowHistory;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applications = Application::with(['assessment'])
            ->whereHas('assessment')
            ->get();

        dump('Number of Applications to Seed: '.$applications->count());

        foreach ($applications as $application) {
            if (rand(0, 9) >= 2) {
                $this->seed($application);
            }
        }
    }

    public function seed(Application $application)
    {
        $staff = User::whereHas('roles')->get();

        $recommendation = Recommendation::factory()->create([
            'application_uuid' => $application->uuid,
            'recommended_by' => User::role('superadmin')->firstOrFail()->id,
        ]);

        if (rand(0, 1) == 1) {
            Remark::factory()->create([
                'remarkable_type' => get_class($recommendation),
                'remarkable_id' => $recommendation->uuid,
                'user_id' => $staff->random(1)->first()->id ?? 1,
            ]);
        }

    }
}
