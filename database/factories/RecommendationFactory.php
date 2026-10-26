<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\FuneralAssistanceType;
use App\Models\ModeOfAssistance;
use App\Models\Recommendation;
use App\Models\User;
use App\Models\Workflow;
use App\Services\RecommendationService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/** @extends Factory<Recommendation> */
class RecommendationFactory extends Factory
{
    public function definition(): array
    {
        $type = FuneralAssistanceType::inRandomOrder()->firstOrFail();

        return [
            'amount_extended' => $this->faker->randomFloat(0, 100, 10000),
            'mode_of_assistance_id' => ModeOfAssistance::inRandomOrder()->firstOrFail()->id,
            'funeral_assistance_type_uuid' => $type->uuid,
            'workflow_uuid' => $type->workflow_uuid,
        ];
    }

    public function create($attributes = [], ?Model $parent = null)
    {
        $models = $this->make($attributes, $parent);
        $previous = auth()->user();
        try {
            $store = function (Recommendation $model) {
                $actor = $model->recommended_by ? User::findOrFail($model->recommended_by) :
                    (auth()->user() ?? User::role('superadmin')->firstOrFail());
                auth()->setUser($actor);
                $application = Application::findOrFail($model->application_uuid);
                $uuid = Workflow::selectable()->whereKey($model->workflow_uuid)->value('uuid') ??
                    Workflow::selectable()->value('uuid');
                $run = app(RecommendationService::class)->store([
                    'workflow_uuid' => $uuid, 'funeral_assistance_type_uuid' => $model->funeral_assistance_type_uuid,
                    'amount_extended' => $model->amount_extended, 'mode_of_assistance_id' => $model->mode_of_assistance_id,
                    'expected_recommendation_uuid' => $application->current_recommendation_uuid,
                    'expected_assignment_uuid' => $application->current_claimant_assignment_uuid,
                ], $application->uuid);
                if ($model->created_at) {
                    $run->update(['created_at' => $model->created_at]);
                }

                return $run;
            };
            $results = $models instanceof Recommendation ? $store($models) : new Collection($models->map($store)->all());
            $this->callAfterCreating($results instanceof Recommendation ? new Collection([$results]) : $results, $parent);

            return $results;
        } finally {
            $previous ? auth()->setUser($previous) : auth()->forgetUser();
        }
    }
}
