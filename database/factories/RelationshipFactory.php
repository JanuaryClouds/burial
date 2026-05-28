<?php

namespace Database\Factories;

use App\Models\Relationship;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Relationship>
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
