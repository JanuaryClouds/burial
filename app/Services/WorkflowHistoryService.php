<?php

namespace App\Services;

use App\Models\WorkflowHistory;

class WorkflowHistoryService
{
    public function __construct()
    {
        //
    }

    public function store(array $data): WorkflowHistory
    {
        return WorkflowHistory::create($data);
    }
}