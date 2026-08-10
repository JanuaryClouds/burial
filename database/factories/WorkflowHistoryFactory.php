<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WorkflowHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkflowHistory>
 */
class WorkflowHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dateIn = $this->faker->dateTimeBetween(now()->subWeek(), now());
        $dateOut = $this->faker->dateTimeBetween($dateIn, now());

        return [
            'reason' => $this->faker->sentence,
            'date_in' => $dateIn,
            'date_out' => $dateOut,
            'created_at' => $this->faker->dateTimeBetween(now()->subWeek(), now())
        ];
    }
}
