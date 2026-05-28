<?php

namespace Database\Factories;

use App\Models\Sex;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sex>
 */
class SexFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Male', 'Female']),
            'remarks' => $this->faker->sentence(8),
        ];
    }
}
