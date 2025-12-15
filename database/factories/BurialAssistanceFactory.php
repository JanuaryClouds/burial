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
    // ! Unused
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'tracking_code' => $this->faker->regexify('[A-Z0-9]{6}'),
            'application_date' => $this->faker->dateTimeBetween('-1 week', now()),
            'amount' => $this->faker->randomFloat(0, 100, 10000),
            'funeraria' => $this->faker->company(),
            'claimant_id' => Claimant::factory(),
            'deceased_id' => Deceased::factory(),
            'status' => $this->faker->randomelement(['pending']),
            'remarks' => $this->faker->sentence(8),
        ];
    }
}
