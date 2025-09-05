<?php

namespace Database\Factories;

use App\Models\Barangay;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Relationship;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Claimant>
 */
class ClaimantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'middle_name'=> $this->faker->optional()->lastName(),
            'last_name'=> $this->faker->lastName(),
            'suffix' => $this->faker->optional()->randomElement(([
                    'Jr.', 'Sr.', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X',
            ])),
            'relationship_to_deceased' => Relationship::inRandomOrder()->first()->id,
            'mobile_number' => $this->faker->regexify('09[0-9]{9}'),
            'address' => $this->faker->address(),
            'barangay_id' => Barangay::inRandomOrder()->first()->id,
        ];
    }
}
