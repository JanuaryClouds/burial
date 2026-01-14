<?php

namespace Database\Factories;

use App\Models\CivilStatus;
use App\Models\Relationship;
use App\Models\Sex;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientBeneficiaryFamily>
 */
class ClientBeneficiaryFamilyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'name' => $this->faker->name(),
            'sex_id' => Sex::inRandomOrder()->first()->id,
            'age' => $this->faker->numberBetween(1, 100),
            'civil_id' => CivilStatus::inRandomOrder()->first()->id,
            'relationship_id' => Relationship::inRandomOrder()->first()->id,
            'occupation' => $this->faker->jobTitle(),
            'income' => $this->faker->randomFloat(0, 100, 10000),
        ];
    }
}
