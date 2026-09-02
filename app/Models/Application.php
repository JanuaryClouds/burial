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
        'client_uuid',
        'beneficiary_uuid',
    ];

    protected static function booted()
    {
        static::creating(function ($application) {
            $year = now()->format('Y');
            $count = self::whereYear('created_at', $year)->count() + 1;
            $application->tracking_no = sprintf('%s-%04d', $year, $count);
            $application->qr_code = 'FUNERAL-' . Str::upper(Str::random(8));
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
     * @return BelongsTo<Client, Application>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_uuid', 'uuid');
    }

    /**
     * @return BelongsTo<Beneficiary, Application>
     */
    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_uuid', 'uuid');
    }

    /**
     * Summary of assessment
     *
     * @return HasOne<Assessment>
     */
    public function assessment(): HasOne
    {
        return $this->hasOne(Assessment::class);
    }

    /**
     * Summary of recommendation
     *
     * @return HasMany<Recommendation, Application>
     */
    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class, 'application_uuid', 'uuid');
    }

    /**
     * Summary of workflow
     * @return BelongsTo<WorkflowStage, Application>
     */
    public function workflowStage(): BelongsTo
    {
        return $this->belongsTo(WorkflowStage::class, 'current_workflow_stage_uuid', 'uuid');
    }

    /**
     * Summary of referral
     *
     * @return HasOne<Referral, Application>
     */
    public function referral(): HasOne
    {
        return $this->hasOne(Referral::class);
    }

    /**
     * Summary of relationship
     * @return BelongsTo<Relationship, Application>
     */
    public function relationship(): BelongsTo
    {
        return $this->belongsTo(Relationship::class);
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
        return self::prefixRelations(
            'recommendations',
            Recommendation::relations()
        );
    }

    protected static function workflowRelations(): array
    {
        return self::prefixRelations(
            'workflow',
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

    public function status(): array
    {
        $status[] = [
            'label' => 'pending',
            'badgeColor' => 'primary'
        ];

        $interviews = $this->client->interviews;
        $assessment = $this->assessment;
        $referral = $this->referral;
        $workflowStage = $this->workflowStage;

        if ($assessment) {
            $status[] = [
                'label' => 'assessment',
                'badgeColor' => 'secondary'
            ];
        }

        if ($workflowStage !== null) {
            $status[] = [
                'label' => 'processing',
                'badgeColor' => 'primary'
            ];

            if ($workflowStage->name === 'Releasing') {
                // $status[] = [
                //     'label' => 'processed',
                //     'badgeColor' => 'secondary',
                // ];

                $status[] = [
                    'label' => 'releasing',
                    'badgeColor' => 'success',
                ];
            }

            if ($workflowStage->name === 'Closing') {
                $status[] = [
                    'label' => 'releasing',
                    'badgeColor' => 'success',
                ];

                $status[] = [
                    'label' => 'closing',
                    'badgeColor' => 'warning',
                ];
            }
        }

        if ($referral) {
            $status[] = [
                'label' => 'referred',
                'badgeColor' => 'success',
            ];
        }

        if ($this->recommendations->count() > 0 && $this->currentRecommendation()->status == 'cancelled') {
            $status[] = [
                'label' => 'cancelled',
                'badgeColor' => 'danger',
            ];
        }

        return $status;
    }

    /**
     * Summary of currentRecommendation
     * @return Recommendation|null
     */
    public function currentRecommendation(): ?Recommendation
    {
        return $this->recommendations()
            ?->latest()
            ->first();
    }

    /**
     * Summary of currentWorkflow
     * @return Workflow|null
     */
    public function currentWorkflow(): ?Workflow
    {
        return $this->currentRecommendation()?->funeralAssistanceType?->workflow;
    }

    /**
     * Summary of currentWorkflowHistory
     * @return Collection|null
     */
    public function currentWorkflowHistory(): ?Collection
    {
        return $this->currentRecommendation()?->workflowHistory;
    }

    /**
     * Summary of currentStage
     * @return WorkflowStage|null
     */
    public function currentStage(): ?WorkflowStage
    {
        return $this->workflowStage;
    }

    /**
     * Summary of fromStage
     * @return WorkflowStage|null
     */
    public function fromStage(): ?WorkflowStage
    {
        $workflow = $this->currentWorkflow();
        
        if (!$workflow) {
            return null;
        }

        $position = $this->workflowStage?->position;

        if ($position < 0) {
            if ($this->currentWorkflowHistory()->count() > 0) {
                $position = 1;
            }
        }

        return $workflow->stages()
            ->firstWhere('position', '=', $position);
    }

    /**
     * Summary of toStage
     * @return WorkflowStage|null
     */
    public function toStage(): ?WorkflowStage
    {
        $workflow = $this->currentWorkflow();

        if (!$workflow) {
            return null;
        }

        $position = $this->workflowStage?->position + 1;

        if ($position == 0 || $position == null) $position = 1;

        if ($position > $workflow->stages()->count()) {
            return null;
        }

        return $workflow->stages()
            ->firstWhere('position', '=', $position);
    }

    /**
     * Summary of previousHistory
     * @return WorkflowHistory|null
     */
    public function previousHistory(): ?WorkflowHistory
    {
        $workflowHistory = $this->currentWorkflowHistory();
        $stage = $this->workflowStage;

        if ($workflowHistory == null || $stage == null) {
            return null;
        }

        return $workflowHistory->firstWhere('to_stage_uuid', '=', $stage->uuid);
    }

    /**
     * Summary of nextHistory
     * @return WorkflowHistory|null
     */
    public function nextHistory(): ?WorkflowHistory
    {
        return $this->currentWorkflowHistory()?->where('from_stage_uuid', '=', $this->workflowStage?->uuid)?->latest()->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Model Scope
    |--------------------------------------------------------------------------
    |
    | Model scopes.
    |
    */
}
