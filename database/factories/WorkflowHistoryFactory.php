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
        return [
            // 'reason' => $this->faker->sentence,
        ];
    }
}
