<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowHistory extends Model
{
    /** @use HasFactory<\Database\Factories\WorkflowHistoryFactory> */
    use HasFactory, HasUuid;

    protected $table = 'workflow_histories';

    protected $fillable = [
        'workflow_uuid',
        'from_stage_uuid',
        'to_stage_uuid',
        'date_in',
        'date_out',
        'reason',
        'processed_by',
        'application_uuid'
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
     * Summary of fromStage
     * @return BelongsTo<WorkflowStage, WorkflowHistory>
     */
    public function fromStage(): BelongsTo
    {
        return $this->belongsTo(WorkflowStage::class, 'from_stage_uuid', 'uuid');
    }

    /**
     * Summary of toStage
     * @return BelongsTo<WorkflowStage, WorkflowHistory>
     */
    public function toStage(): BelongsTo
    {
        return $this->belongsTo(WorkflowStage::class, 'to_stage_uuid', 'uuid');
    }

    /**
     * Summary of processedBy
     * @return BelongsTo<User, WorkflowHistory>
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by', 'id');
    }

    /**
     * Summary of application
     * @return BelongsTo<Application, WorkflowHistory>
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_uuid', 'uuid');
    }

    /**
     * Summary of recommendation
     * @return BelongsTo<Recommendation, WorkflowHistory>
     */
    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(Recommendation::class, 'recommendation_uuid', 'uuid');
    }
}
