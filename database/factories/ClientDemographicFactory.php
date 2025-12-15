<?php

namespace Database\Factories;

use App\Models\Nationality;
use App\Models\Religion;
use App\Models\Sex;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientDemographic>
 */
class ClientDemographicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'sex_id' => Sex::inRandomOrder()->first()->id,
            'religion_id' => Religion::inRandomOrder()->first()->id,
            'nationality_id' => Nationality::inRandomOrder()->first()->id,
        ];
    }
}
