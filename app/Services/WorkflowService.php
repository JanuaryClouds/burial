<?php

namespace App\Services;

use App\Models\Workflow;

class WorkflowService
{
    public function __construct()
    {
        //
    }

    public function index()
    {
        return Workflow::with(['stages', 'transitions', 'funeralAssistanceTypes'])
            ->get()
            ->map(function ($workflow) {
                return [
                    'uuid' => $workflow->uuid,
                    'name' => $workflow->name,
                    'description' => $workflow->description,
                    'stages_count' => $workflow->stages->count(),
                    'transitions_count' => $workflow->transitions->count(),
                    'funeral_assistance_types_count' => $workflow->funeralAssistanceTypes()->count(),
                    'show_route' => route('workflow.show', $workflow)
                ];
            });
    }

    /**
     * Summary of store
     * @param array $data
     * @return Workflow
     */
    public function store(array $data): Workflow
    {
        $workflow = Workflow::create($data);
        return $workflow;
    }

    /**
     * Summary of update
     * @param array $data
     * @param Workflow $workflow
     * @return bool|int|mixed
     */
    public function update(array $data, Workflow $workflow): bool
    {
        return $workflow->update($data);
    }

    public function destroy()
    {
        //
    }
}