<?php

namespace App\Services;

use App\Models\FuneralAssistanceType;

class FuneralAssistanceTypeService
{
    public function __construct()
    {
        //
    }

    public function index()
    {
        //
    }

    public function store(array $data)
    {
        return FuneralAssistanceType::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'workflow_uuid' => $data['workflowUuid'],
        ]);
    }

    public function edit()
    {
        //
    }

    /**
     * Summary of update
     *
     * @return bool|int
     */
    public function update(array $data, FuneralAssistanceType $funeralAssistanceType)
    {
        return $funeralAssistanceType->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'workflow_uuid' => $data['workflowUuid'],
        ]);
    }

    public function destroy()
    {
        //
    }
}
