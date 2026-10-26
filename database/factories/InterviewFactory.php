<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Interview;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Interview>
 */
class InterviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function configure(): static
    {
        return $this->afterMaking(function (Interview $interview) {
            if (! $interview->claimant_assignment_uuid && $interview->client_uuid) {
                $interview->claimant_assignment_uuid = Application::where('client_uuid', $interview->client_uuid)->value('current_claimant_assignment_uuid');
            }
        });
    }

    public function definition(): array
    {
        return [];
    }
}
