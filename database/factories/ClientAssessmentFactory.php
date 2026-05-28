<?php

namespace Database\Factories;

use App\Models\ClientAssessment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends Factory<ClientAssessment>
 */
class ClientAssessmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'problem_presented' => $this->faker->sentence(8),
            'assessment' => $this->faker->sentence(8),
        ];
    }
}
