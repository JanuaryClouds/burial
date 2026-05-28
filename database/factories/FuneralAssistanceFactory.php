<?php

namespace Database\Factories;

use App\Models\FuneralAssistance;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends Factory<FuneralAssistance>
 */
class FuneralAssistanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
        ];
    }
}
