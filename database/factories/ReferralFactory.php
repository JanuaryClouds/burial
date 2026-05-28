<?php

namespace Database\Factories;

use App\Models\Referral;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends Factory<Referral>
 */
class ReferralFactory extends Factory
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
            'referral_to' => $this->faker->name(),
            'remarks' => 'seeder generated',
        ];
    }
}
