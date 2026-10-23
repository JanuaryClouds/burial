<?php

namespace Database\Factories;

use App\Models\WorkflowStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Draft attributes only. Persist stages through WorkflowVersionService.
 *
 * @extends Factory<WorkflowStage>
 */
class WorkflowStageFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => $this->faker->words(2, true), 'description' => $this->faker->sentence()];
    }
}
