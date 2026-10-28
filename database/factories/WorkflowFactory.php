<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Workflow;
use App\Services\Auth\SeederAccountContext;
use App\Services\WorkflowVersionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/** @extends Factory<Workflow> */
class WorkflowFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => $this->faker->unique()->words(3, true), 'description' => $this->faker->sentence()];
    }

    public function create($attributes = [], ?Model $parent = null)
    {
        $models = $this->make($attributes, $parent);
        $previous = auth()->user();
        auth()->setUser($previous ?? User::role('superadmin')->firstOrFail());
        try {
            $publish = fn (Workflow $model) => SeederAccountContext::run(fn () => app(WorkflowVersionService::class)->publish([
                'name' => $model->name, 'description' => $model->description,
                'stages' => [['name' => 'Review', 'description' => 'Review records', 'permission_id' => 'Workflow Review']],
            ]));
            $results = $models instanceof Workflow ? $publish($models) : new Collection($models->map($publish)->all());
            $this->callAfterCreating($results instanceof Workflow ? new Collection([$results]) : $results, $parent);

            return $results;
        } finally {
            $previous ? auth()->setUser($previous) : auth()->forgetUser();
        }
    }
}
