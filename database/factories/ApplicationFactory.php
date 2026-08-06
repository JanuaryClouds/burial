<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Relationship;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'relationship_id' => Relationship::inRandomOrder()->first()->id,
        ];
    }
}
