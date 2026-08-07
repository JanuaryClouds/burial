<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowStage extends Model
{
    /** @use HasFactory<\Database\Factories\WorkflowStageFactory> */
    use HasFactory, HasUuid;

    protected $table = 'workflow_stages';

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Summary of applications
     * @return HasMany<Application, WorkflowStage>
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'current_workflow_stage_uuid', 'uuid');
    }

    /**
     * Summary of workflowHistories
     * @return HasMany<WorkflowHistory, WorkflowStage>
     */
    public function nextSteps(): HasMany
    {
        return $this->hasMany(WorkflowHistory::class, 'to_stage_uuid', 'uuid');
    }

    /**
     * Summary of previousSteps
     * @return HasMany<WorkflowHistory, WorkflowStage>
     */
    public function previousSteps(): HasMany
    {
        return $this->hasMany(WorkflowHistory::class, 'from_stage_uuid', 'uuid');
    }
}
