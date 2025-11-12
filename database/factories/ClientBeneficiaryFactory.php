<?php

namespace Database\Factories;

use App\Models\Barangay;
use App\Models\Religion;
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
            'suffix' => $this->faker->optional()->randomElement(['Jr.', 'Sr.', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X']),
            'sex_id' => Sex::inRandomOrder()->first()->id,
            'religion_id' => Religion::inRandomOrder()->first()->id,
            'date_of_birth' => $this->faker->date('Y-m-d'),
            'date_of_death' => $this->faker->dateTimeBetween('-1 week', now()),
            'place_of_birth' => $this->faker->city(),
            'barangay_id' => Barangay::inRandomOrder()->first()->id,
        ];
    }
}
