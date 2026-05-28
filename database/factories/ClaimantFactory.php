<?php

namespace Database\Factories;

use App\Models\Claimant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends Factory<Claimant>
 */
class ClaimantFactory extends Factory
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
            'city' => 'Taguig City',
        ];
    }
}
