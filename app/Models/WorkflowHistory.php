<?php

namespace App\Models;

use App\Traits\HasRemarks;
use App\Traits\HasUuid;
use Database\Factories\WorkflowHistoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowHistory extends Model
{
    /** @use HasFactory<WorkflowHistoryFactory> */
    use HasFactory, HasRemarks, HasUuid;

    protected $table = 'workflow_histories';

    protected $fillable = [
        'recommendation_uuid',
        'from_stage_uuid',
        'to_stage_uuid',
        'date_in',
        'date_out',
        'reason',
        'processed_by',
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
     *
     * @return BelongsTo<WorkflowStage, $this>
     */
    public function fromStage(): BelongsTo
    {
        return $this->belongsTo(WorkflowStage::class, 'from_stage_uuid', 'uuid')->withTrashed();
    }

    /**
     * Summary of toStage
     *
     * @return BelongsTo<WorkflowStage, $this>
     */
    public function toStage(): BelongsTo
    {
        return $this->belongsTo(WorkflowStage::class, 'to_stage_uuid', 'uuid')->withTrashed();
    }

    /**
     * Summary of processedBy
     *
     * @return BelongsTo<User, $this>
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by', 'id');
    }

    /**
     * Summary of recommendation
     *
     * @return BelongsTo<Recommendation, $this>
     */
    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(Recommendation::class, 'recommendation_uuid', 'uuid');
    }
}
