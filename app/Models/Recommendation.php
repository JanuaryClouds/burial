<?php

namespace App\Models;

use App\Traits\HasRemarks;
use App\Traits\HasUuid;
use Database\Factories\RecommendationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** @property string|null $claimant_assignment_uuid */
class Recommendation extends Model
{
    /** @use HasFactory<RecommendationFactory> */
    use HasFactory, HasRemarks, HasUuid;

    protected $table = 'recommendations';

    public function claimantAssignment(): BelongsTo
    {
        return $this->belongsTo(ApplicationClaimantAssignment::class, 'claimant_assignment_uuid', 'uuid');
    }

    protected $fillable = [
        'claimant_assignment_uuid',
        'application_uuid',
        'workflow_uuid',
        'funeral_assistance_type_uuid',
        'amount_extended',
        'mode_of_assistance_id',
        'recommended_by',
        'status',
        'approved_at',
    ];

    protected static function booted(): void
    {
        static::updating(function (Recommendation $recommendation) {
            if ($recommendation->isDirty(['workflow_uuid', 'application_uuid'])) {
                throw new \LogicException('A recommendation stays pinned to its original workflow. Create a new recommendation.');
            }
        });
        static::deleting(fn () => throw new \LogicException('Recommendations and their workflow history must be preserved.'));
    }

    /** @return BelongsTo<Workflow, $this> */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_uuid', 'uuid')->withTrashed();
    }

    /**
     * Summary of application
     *
     * @return BelongsTo<Application, $this>
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_uuid', 'uuid');
    }

    /**
     * Summary of modeOfAssistance
     *
     * @return BelongsTo<ModeOfAssistance, $this>
     */
    public function modeOfAssistance(): BelongsTo
    {
        return $this->belongsTo(ModeOfAssistance::class, 'mode_of_assistance_id', 'id');
    }

    /**
     * Summary of recommendedBy
     *
     * @return BelongsTo<User, $this>
     */
    public function recommendedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recommended_by', 'id');
    }

    /**
     * Summary of assistance
     *
     * @return BelongsTo<FuneralAssistanceType, $this>
     */
    public function funeralAssistanceType(): BelongsTo
    {
        return $this->belongsTo(FuneralAssistanceType::class, 'funeral_assistance_type_uuid', 'uuid');
    }

    /**
     * Summary of workflowHistory
     *
     * @return HasMany<WorkflowHistory, $this>
     */
    public function workflowHistory(): HasMany
    {
        return $this->hasMany(WorkflowHistory::class, 'recommendation_uuid', 'uuid');
    }

    public static function relations(): array
    {
        return [
            'application',
            'claimantAssignment',
            'modeOfAssistance',
            'recommendedBy',
            'funeralAssistanceType',
            'workflow',
            'workflowHistory',
        ];
    }
}
