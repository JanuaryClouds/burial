<?php

namespace App\Services;

use App\Models\Assessment;

class AssessmentService
{
    public function __construct()
    {
        //
    }

    /**
     * Summary of store
     */
    public function store(array $data, string $applicationUuid): Assessment
    {
        $data['application_uuid'] = $applicationUuid;

        return Assessment::create($data);
    }
}
