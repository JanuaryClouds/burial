<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Relationship>
 */
class RelationshipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sampleRelationships = [
            'Father', 'Mother', 'Brother', 'Sister', 'Grandfather', 'Grandmother',
        ];

        return [
            'name' => $this->faker->randomElement($sampleRelationships),
        ];
    }
}
