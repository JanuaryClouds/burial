<?php

namespace App\Models;

use App\Traits\HasRelationSets;
use App\Traits\HasUuid;
use Database\Factories\ApplicationFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Application extends Model
{
    /** @use HasFactory<ApplicationFactory> */
    use HasFactory, HasRelationSets, HasUuid;

    protected $table = 'applications';

    protected $fillable = [
        'current_workflow_stage_uuid',
        'current_recommendation_uuid',
        'client_uuid',
        'beneficiary_uuid',
        'relationship_id',
    ];

    protected static function booted()
    {
        static::creating(function ($application) {
            $year = now()->format('Y');
            $count = self::whereYear('created_at', $year)->count() + 1;
            $application->tracking_no = sprintf('%s-%04d', $year, $count);
            $application->qr_code = 'FUNERAL-'.Str::upper(Str::random(8));
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Model Relations
    |--------------------------------------------------------------------------
    |
    | Model relationships.
    |
    */

    /**
     * Get the client that owns the application.
     *
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_uuid', 'uuid');
    }

    /**
     * @return BelongsTo<Beneficiary, $this>
     */
    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_uuid', 'uuid');
    }

    /**
     * Summary of assessment
     *
     * @return HasOne<Assessment, $this>
     */
    public function assessment(): HasOne
    {
        return $this->hasOne(Assessment::class);
    }

    /** @return BelongsTo<Recommendation, $this> */
    public function activeRecommendation(): BelongsTo
    {
        return $this->belongsTo(Recommendation::class, 'current_recommendation_uuid', 'uuid');
    }

    /** @return HasMany<Recommendation, $this> */
    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class, 'application_uuid', 'uuid');
    }

    /**
     * Summary of workflow
     *
     * @return BelongsTo<WorkflowStage, $this>
     */
    public function workflowStage(): BelongsTo
    {
        return $this->belongsTo(WorkflowStage::class, 'current_workflow_stage_uuid', 'uuid')->withTrashed();
    }

    /**
     * Summary of referral
     *
     * @return HasOne<Referral, $this>
     */
    public function referral(): HasOne
    {
        return $this->hasOne(Referral::class);
    }

    /**
     * Summary of relationship
     *
     * @return BelongsTo<Relationship, $this>
     */
    public function relationship(): BelongsTo
    {
        return $this->belongsTo(Relationship::class);
    }

    /**
     * Summary of rejection
     *
     * @return HasOne<Rejection, $this>
     */
    public function rejection(): HasOne
    {
        return $this->hasOne(Rejection::class, 'application_uuid', 'uuid');
    }

    /**
     * Summary of cancellation
     *
     * @return HasOne<Cancellation, $this>
     */
    public function cancellation(): HasOne
    {
        return $this->hasOne(Cancellation::class, 'application_uuid', 'uuid');
    }

    /**
     * Summary of closure
     *
     * @return HasOne<Closure, $this>
     */
    public function closure(): HasOne
    {
        return $this->hasOne(Closure::class, 'application_uuid', 'uuid');
    }

    protected static function clientRelations(): array
    {
        return self::prefixRelations(
            'client',
            Client::relations()
        );
    }

    protected static function beneficiaryRelations(): array
    {
        return self::prefixRelations(
            'beneficiary',
            Beneficiary::relations()
        );
    }

    protected static function recommendationRelations(): array
    {
        return [
            ...self::prefixRelations('recommendations', Recommendation::relations()),
            ...self::prefixRelations('activeRecommendation', Recommendation::relations()),
        ];
    }

    protected static function workflowRelations(): array
    {
        return self::prefixRelations(
            'activeRecommendation.workflow',
            Workflow::relations()
        );
    }

    public static function relations(string ...$groups): array
    {
        return collect($groups)
            ->flatMap(fn ($group) => match ($group) {
                'client' => self::clientRelations(),
                'beneficiary' => self::beneficiaryRelations(),
                'recommendation' => self::recommendationRelations(),
                'assessment' => ['assessment'],
                'referral' => ['referral'],
                'rejection' => ['rejection'],
                'cancellation' => ['cancellation'],
                'closure' => ['closure'],
                'workflow' => self::workflowRelations(),
                'workflowHistory' => ['workflowHistory'],
                'relationship' => ['relationship'],
                default => [],
            })
            ->unique()
            ->values()
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Model Functions
    |--------------------------------------------------------------------------
    |
    | Functions exclusive to this model.
    |
    */

    public function status(?string $startDate = null, ?string $endDate = null): array
    {
        $status[] = [
            'label' => 'pending',
            'badgeColor' => 'primary',
        ];

        $interviews = $this->client->interviews;
        if ($startDate && $endDate) {
            $interviews = $interviews->whereBetween('schedule', [$startDate, $endDate]);
        }

        $referral = $this->referral ? $this->referral
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }) : null;

        $rejection = $this->rejection ? $this->rejection
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }) : null;

        $cancellation = $this->cancellation ? $this->cancellation
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }) : null;

        $closure = $this->closure ? $this->closure
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }) : null;

        $workflowStage = $this->workflowStage ? $this->workflowStage
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }) : null;

        if ($interviews->count() > 0) {
            $status[] = [
                'label' => 'assessment',
                'badgeColor' => 'secondary',
            ];
        }

        if ($workflowStage !== null) {
            $status[] = [
                'label' => 'processing',
                'badgeColor' => 'primary',
            ];

            // if ($workflowStage->name === 'Releasing') {
            //     $status[] = [
            //         'label' => 'releasing',
            //         'badgeColor' => 'success',
            //     ];
            // }

            // if ($workflowStage->name === 'Closing') {
            //     $status[] = [
            //         'label' => 'releasing',
            //         'badgeColor' => 'success',
            //     ];
            // }
        }

        if ($referral) {
            $status[] = [
                'label' => 'referred',
                'badgeColor' => 'success',
            ];
        }

        if ($cancellation) {
            $status[] = [
                'label' => 'cancelled',
                'badgeColor' => 'danger',
            ];
        }

        if ($rejection) {
            $status[] = [
                'label' => 'rejected',
                'badgeColor' => 'danger',
            ];
        }

        if ($closure) {
            $status[] = [
                'label' => 'closed',
                'badgeColor' => 'success',
            ];
        }

        return $status;
    }

    public function currentStatus(): array
    {
        return $this->status()[count($this->status()) - 1];
    }

    /**
     * Summary of finishedInterview
     */
    public function finishedInterview(): bool
    {
        return $this->client->interviews()->where('status', '=', 'done')->exists();
    }

    /**
     * Summary of currentRecommendation
     */
    public function currentRecommendation(): ?Recommendation
    {
        return $this->activeRecommendation;
    }

    /**
     * Summary of currentWorkflow
     */
    public function currentWorkflow(): ?Workflow
    {
        return $this->currentRecommendation()?->workflow;
    }

    /**
     * Summary of currentWorkflowHistory
     */
    public function currentWorkflowHistory(): ?Collection
    {
        return $this->currentRecommendation()?->workflowHistory;
    }

    /**
     * Summary of currentStage
     */
    public function currentStage(): ?WorkflowStage
    {
        return $this->workflowStage;
    }

    /**
     * Summary of fromStage
     */
    public function fromStage(): ?WorkflowStage
    {
        $stage = $this->currentStage();

        return $stage && $stage->workflow_uuid === $this->currentWorkflow()?->uuid ? $stage : null;
    }

    /**
     * Summary of toStage
     */
    public function toStage(): ?WorkflowStage
    {
        $workflow = $this->currentWorkflow();
        if (! $workflow) {
            return null;
        }
        $current = $this->currentStage();
        if ($current && $current->workflow_uuid !== $workflow->uuid) {
            return null;
        }

        return $workflow->stages()->where('position', ($current ? $current->position : 0) + 1)->first();
    }

    /**
     * Summary of previousHistory
     */
    public function previousHistory(): ?WorkflowHistory
    {
        if (! $this->current_workflow_stage_uuid) {
            return null;
        }

        return $this->currentRecommendation()?->workflowHistory()
            ->where('to_stage_uuid', $this->current_workflow_stage_uuid)->latest()->first();
    }

    /**
     * Summary of nextHistory
     */
    public function nextHistory(): ?WorkflowHistory
    {
        return $this->currentRecommendation()?->workflowHistory()->where('from_stage_uuid', $this->workflowStage?->uuid)->latest()->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Model Scope
    |--------------------------------------------------------------------------
    |
    | Model scopes.
    |
    */

    public function scopeIndex(
        $query,
        ?int $userId = null,
        ?string $startDate = null,
        ?string $endDate = null,
    ) {
        return $query->with([
            'client',
            'client.interviews',
            'client.user',
            'beneficiary',
            'relationship',
            'assessment',
            'recommendations',
            'activeRecommendation.workflow',
            'referral',
            'rejection',
            'cancellation',
            'closure',
            'workflowStage',
        ])
            ->when($userId, function ($query) use ($userId) {
                $query->whereHas('client.user', function ($subQuery) use ($userId) {
                    $subQuery->where('id', $userId);
                });
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            });
    }

    public function scopeTotal($query, ?string $startDate = null, ?string $endDate = null)
    {
        return $query->when(Auth::user()->roles()->count() == 0, function ($query) {
            $query->whereHas('client', function ($query) {
                $query->where('user_id', Auth::id());
            });
        })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            });
    }

    public function scopePerRelationship($query)
    {
        return $query->with(['relationship'])
            ->groupBy('relationship_id')
            ->selectRaw('relationship_id, count(*) as total');
    }
}
