<?php

namespace Database\Factories;

use App\Models\FuneralAssistanceType;
use App\Models\ModeOfAssistance;
use App\Models\Recommendation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Recommendation>
 */
class RecommendationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount_extended' => $this->faker->randomFloat(0, 100, 10000),
            'mode_of_assistance_id' => ModeOfAssistance::inRandomOrder()->firstOrFail()->id,
            'funeral_assistance_type_uuid' => FuneralAssistanceType::inRandomOrder()->firstOrFail()->uuid,
            'created_at' => $this->faker->dateTimeBetween(now()->subWeek(), now())
        ];
    }
}
