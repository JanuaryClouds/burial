<?php

namespace Database\Factories;

use App\Models\Sex;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deceased>
 */
class DeceasedFactory extends Factory
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
            'middle_name' => $this->faker->optional()->lastName(),
            'last_name'=> $this->faker->lastName(),
            'suffix' => $this->faker->optional()->randomElement([
                'Jr.', 'Sr.', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X',
            ]),
            'date_of_birth' => $this->faker->date('Y-m-d'),
            'date_of_death' => $this->faker->dateTimeBetween('-2 years', now()),
            'gender' => Sex::inRandomOrder()->first()->id,
        ];
    }
}
