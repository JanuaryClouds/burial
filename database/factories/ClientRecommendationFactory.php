<?php

namespace Database\Factories;

use App\Models\Assistance;
use App\Models\ModeOfAssistance;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientRecommendation>
 */
class ClientRecommendationFactory extends Factory
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
            'assistance_id' => fn () => Assistance::where('name', 'Burial')->firstOrFail()->id,
            'referral' => $this->faker->name(),
            'amount' => $this->faker->randomFloat(0, 100, 10000),
            'moa_id' => fn () => ModeOfAssistance::inRandomOrder()->firstOrFail()->id,
            'remarks' => 'seeder generated',
        ];
    }
}
