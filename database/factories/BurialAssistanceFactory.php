<?php

namespace Database\Factories;

use App\Models\BurialAssistance;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends Factory<BurialAssistance>
 */
class BurialAssistanceFactory extends Factory
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
            'funeraria' => $this->faker->company(),
            'remarks' => 'seeder generated',
        ];
    }
}
