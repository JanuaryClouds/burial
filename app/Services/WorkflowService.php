<?php

namespace App\Services;

use App\Models\Workflow;

class WorkflowService
{
    public function store(array $data): Workflow
    {
        return app(WorkflowVersionService::class)->publish($data);
    }

    public function update(array $data, Workflow $workflow): Workflow
    {
        return app(WorkflowVersionService::class)->publish($data, $workflow);
    }
}
