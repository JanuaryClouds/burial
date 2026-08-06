<?php

namespace App\Services;

use App\Models\Recommendation;

class RecommendationService
{
    public function __construct()
    {
        //
    }

    public function store(array $data, string $applicationUuid): Recommendation
    {
        $data['application_uuid'] = $applicationUuid;
        $data['recommended_by'] = auth()->user()->id;
        $recommendation = Recommendation::create($data);

        return $recommendation;
    }
}
