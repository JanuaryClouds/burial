<?php

namespace Database\Factories;

use App\Models\CivilStatus;
use App\Models\Education;
use App\Models\Relationship;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientSocialInfo>
 */
class ClientSocialInfoFactory extends Factory
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
            'relationship_id' => Relationship::inRandomOrder()->first()->id,
            'civil_id' => CivilStatus::inRandomOrder()->first()->id,
            'education_id' => Education::inRandomOrder()->first()->id,
            'income' => $this->faker->randomFloat(0, 100, 10000),
            'philhealth' => $this->faker->numberBetween(1000, 9999),
            'skill' => $this->faker->jobTitle(),
        ];
    }
}
