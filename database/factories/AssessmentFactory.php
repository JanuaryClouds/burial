<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Assessment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assessment>
 */
class AssessmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function configure(): static
    {
        return $this->afterMaking(function (Assessment $assessment) {
            if (! $assessment->claimant_assignment_uuid && $assessment->application_uuid) {
                $assessment->claimant_assignment_uuid = Application::whereKey($assessment->application_uuid)->value('current_claimant_assignment_uuid');
            }
        });
    }

    public function definition(): array
    {
        return [
            'swa' => $this->faker->sentences(2, true),
            'problem_presented' => $this->faker->sentences(2, true),
        ];
    }
}
