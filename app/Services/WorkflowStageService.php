<?php

namespace App\Services;

use App\Models\WorkflowStage;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class WorkflowStageService
{
    public function __construct()
    {
        //
    }

    public function store(array $data): WorkflowStage
    {   
        return DB::transaction(function () use ($data) {
            return WorkflowStage::create($data);
        });
    }
}