<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Sex;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientBeneficiary>
 */
class ClientBeneficiaryFactory extends Factory
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
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->optional()->lastName(),
            'last_name' => $this->faker->lastName(),
            'sex_id' => Sex::inRandomOrder()->first()->id,
            'date_of_birth' => $this->faker->date('Y-m-d'),
            'place_of_birth' => $this->faker->city(),
        ];
    }
}
