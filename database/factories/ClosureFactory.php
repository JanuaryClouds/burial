<?php

namespace Database\Factories;

use App\Models\Closure;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Closure>
 */
class ClosureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reason' => $this->faker->sentences(3, true),
        ];
    }
}
