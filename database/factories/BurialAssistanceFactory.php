<?php

namespace Database\Factories;

use App\Models\Claimant;
use App\Models\Deceased;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BurialAssistance>
 */
class BurialAssistanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tracking_code' => $this->faker->regexify('[A-Z0-9]{6}'),
            'application_date' => $this->faker->dateTimeBetween('-1 week', now()),
            'funeraria' => $this->faker->company(),
            'claimant_id' => Claimant::factory(),
            'deceased_id' => Deceased::factory(),
            'status' => $this->faker->randomelement( ['pending', 'processing', 'approved', 'released', 'rejected']),
            'remarks' => $this->faker->sentence(8),
        ];
    }
}
