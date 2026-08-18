<?php

namespace Database\Factories;

use App\Models\BeneficiaryFamily;
use App\Models\CivilStatus;
use App\Models\Relationship;
use App\Models\Sex;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BeneficiaryFamily>
 */
class BeneficiaryFamilyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'sex_id' => Sex::inRandomOrder()->first()->id,
            'age' => $this->faker->numberBetween(1, 100),
            'civil_id' => CivilStatus::inRandomOrder()->first()->id,
            'relationship_id' => Relationship::inRandomOrder()->first()->id,
            'occupation' => $this->faker->jobTitle(),
            'income' => $this->faker->randomFloat(0, 100, 10000),
            'created_at' => $this->faker->dateTimeBetween(now()->subWeek(), now())
        ];
    }
}
