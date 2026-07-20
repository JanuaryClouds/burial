<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\ModeOfAssistance;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
    public function definition(): array
    {
        return [
            'swa' => $this->faker->sentences(2, true),
            'problem_presented' => $this->faker->sentences(2, true),
        ];
    }
}
