<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowStage extends Model
{
    /** @use HasFactory<\Database\Factories\WorkflowStageFactory> */
    use HasFactory, HasUuid;

    protected $table = 'workflow_stages';

    protected $fillable = [
        'name',
        'description',
        'workflow_uuid',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Model Relationships.
    |
    */

    /**
     * Summary of applications
     * @return HasMany<Application, WorkflowStage>
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'current_workflow_stage_uuid', 'uuid');
    }

    /**
     * Summary of workflow
     * @return BelongsTo<Workflow, WorkflowStage>
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_uuid', 'uuid');
    }

    /**
     * Summary of incomingStages
     * @return HasMany<WorkflowTransition, WorkflowStage>
     */
    public function incomingStages(): HasMany
    {
        return $this->HasMany(WorkflowTransition::class, 'to_stage_uuid', 'uuid');
    }

    /**
     * Summary of outgoingStages
     * @return HasMany<WorkflowTransition, WorkflowStage>
     */
    public function outgoingStages(): HasMany
    {
        return $this->hasMany(WorkflowTransition::class, 'from_stage_uuid', 'uuid');
    }

    /**
     * Summary of workflowHistories
     * @return HasMany<WorkflowHistory, WorkflowStage>
     */
    public function nextStages(): HasMany
    {
        return $this->hasMany(WorkflowHistory::class, 'to_stage_uuid', 'uuid');
    }

    /**
     * Summary of previousSteps
     * @return HasMany<WorkflowHistory, WorkflowStage>
     */
    public function previousStages(): HasMany
    {
        return $this->hasMany(WorkflowHistory::class, 'from_stage_uuid', 'uuid');
    }
}
