<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission;

class WorkflowTransition extends Model
{
    /** @use HasFactory<\Database\Factories\WorkflowTransitionFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    protected $table = 'workflow_transitions';

    protected $fillable = [
        'workflow_uuid',
        'from_stage_uuid',
        'to_stage_uuid',
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
     * Summary of workflow
     * @return BelongsTo<Workflow, WorkflowTransition>
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_uuid', 'uuid');
    }

    /**
     * 
     * @return BelongsTo<WorkflowStage, WorkflowTransition>
     */
    public function fromStage(): BelongsTo
    {
        return $this->belongsTo(WorkflowStage::class, 'from_stage_uuid');
    }

    /**
     * Summary of toStage
     * @return BelongsTo<WorkflowStage, WorkflowTransition>
     */
    public function toStage(): BelongsTo
    {
        return $this->belongsTo(WorkflowStage::class, 'to_stage_uuid');
    }
}
