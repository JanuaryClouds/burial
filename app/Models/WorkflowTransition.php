<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission;

class WorkflowTransition extends Model
{
    /** @use HasFactory<\Database\Factories\WorkflowTransitionFactory> */
    use HasFactory, HasUuid;

    protected $table = 'workflow_transitions';

    protected $fillable = [
        'workflow_uuid',
        'from_stage_uuid',
        'to_stage_uuid',
        'permission_id',
    ];

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

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }
}
