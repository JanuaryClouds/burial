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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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
        static::created(function (Application $application) {
            $assignment = ApplicationClaimantAssignment::create([
                'application_uuid' => $application->uuid,
                'client_uuid' => $application->client_uuid,
                'relationship_id' => $application->relationship_id,
                'sequence' => 1,
                'started_at' => $application->created_at,
                'identity_snapshot' => ApplicationClaimantAssignment::snapshot(Client::findOrFail($application->client_uuid)),
            ]);
            $application->forceFill(['current_claimant_assignment_uuid' => $assignment->uuid])->saveQuietly();
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

    public function claimantAssignments(): HasMany
    {
        return $this->hasMany(ApplicationClaimantAssignment::class, 'application_uuid', 'uuid');
    }

    public function currentInterviews(): HasMany
    {
        return $this->hasMany(Interview::class, 'claimant_assignment_uuid', 'current_claimant_assignment_uuid');
    }

    public function assertAssignment(?string $expected): void
    {
        if (! $expected || $this->current_claimant_assignment_uuid !== $expected) {
            throw ValidationException::withMessages(['application' => 'The claimant changed. Reload the application before continuing.']);
        }
    }

    public function isFormerClaimant(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }
        $currentUserId = $this->relationLoaded('client') ? $this->getRelation('client')->user_id
            : $this->client()->value('user_id');
        if ($currentUserId === $userId) {
            return false;
        }
        if ($this->relationLoaded('claimantAssignments') && $this->claimantAssignments->every(fn ($assignment) => $assignment->relationLoaded('client'))) {
            return $this->claimantAssignments->contains(fn ($assignment) => $assignment->client->user_id === $userId);
        }

        return $this->claimantAssignments()->whereHas('client', fn ($q) => $q->where('user_id', $userId))->exists();
    }

    public function isActive(): bool
    {
        foreach (['rejection', 'cancellation', 'referral', 'closure'] as $relation) {
            if ($this->relationLoaded($relation) ? $this->getRelation($relation) !== null : $this->{$relation}()->exists()) {
                return false;
            }
        }

        return true;
    }

    public function hasPaymentBlock(): bool
    {
        return DB::table('cheques')->where('application_uuid', $this->uuid)
            ->where(fn ($q) => $q->whereIn('status', ['issued', 'claimed'])->orWhereNotNull('date_claimed'))->exists();
    }

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
        return $this->hasOne(Assessment::class, 'claimant_assignment_uuid', 'current_claimant_assignment_uuid');
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
        return [...self::prefixRelations('client', Client::relations()), 'currentInterviews'];
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

        $interviews = $this->currentInterviews;
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

        if ($interviews->where('status', 'done')->count() > 0) {
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
        $status = $this->status();

        return $status[count($status) - 1];
    }

    /**
     * Summary of finishedInterview
     */
    public function finishedInterview(): bool
    {
        return $this->relationLoaded('currentInterviews')
            ? $this->currentInterviews->contains('status', 'done')
            : $this->currentInterviews()->where('status', 'done')->exists();
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
            'currentInterviews',
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
                $query->where(function ($q) use ($userId) {
                    $q->whereHas('client', fn ($c) => $c->where('user_id', $userId))
                        ->orWhereHas('claimantAssignments.client', fn ($c) => $c->where('user_id', $userId));
                });
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            });
    }

    public function scopeTotal($query, ?string $startDate = null, ?string $endDate = null)
    {
        return $query->when(Auth::user()->roles()->count() == 0, function ($query) {
            $query->where(function ($q) {
                $q->whereHas('client', fn ($c) => $c->where('user_id', Auth::id()))
                    ->orWhereHas('claimantAssignments.client', fn ($c) => $c->where('user_id', Auth::id()));
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
