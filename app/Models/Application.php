<?php

namespace App\Models;

use App\Traits\HasRelationSets;
use App\Traits\HasUuid;
use Database\Factories\ApplicationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Application extends Model
{
    /** @use HasFactory<ApplicationFactory> */
    use HasFactory, HasRelationSets, HasUuid;

    protected $table = 'applications';

    protected $fillable = [
        'tracking_no',
        'client_uuid',
        'beneficiary_uuid',
    ];

    protected static function booted()
    {
        static::creating(function ($application) {
            $year = now()->format('Y');
            $count = self::whereYear('created_at', $year)->count() + 1;
            $application->tracking_no = sprintf('%s-%04d', $year, $count);
            $application->qr_code = 'APP-' . Str::upper(Str::random(8));
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
     * Summary of processLogs
     *
     * @return HasMany<ProcessLog>
     */
    public function processLogs(): HasMany
    {
        return $this->hasMany(ProcessLog::class);
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

    public static function relations(string ...$groups): array
    {
        return collect($groups)
            ->flatMap(fn ($group) => match ($group) {
                'client' => self::clientRelations(),
                'beneficiary' => self::beneficiaryRelations(),
                'recommendation' => self::recommendationRelations(),
                'assessment' => ['assessment'],
                'processLogs' => ['processLogs'],
                'referral' => ['referral'],
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

    public function status(): string
    {
        $status = 'Pending';

        $interviews = $this->client->interviews;
        $assessment = $this->assessment;
        $recommendations = $this->recommendations;
        $referral = $this->referral;
        $processLogs = $this->processLogs;

        if ($interviews && $interviews->count() > 0) {
            if ($interviews->first()->status == 'done') {
                $status = 'Interviewed';
            } else {
                $status = 'Scheduled';
            }
        }

        if ($assessment) {
            $status = 'Assessed';
        }

        if ($recommendations->isNotEmpty()) {
            $approved_recommendation = $recommendations->where('status', 'approved');

            if ($approved_recommendation) {
                $status = 'Recommended';
            }
        }

        if ($referral) {
            $status = 'Referred';
        }

        if ($processLogs->isNotEmpty()) {
            $latestLog = $processLogs->first();
            $latestStep = $latestLog->loggable()->order_no;
            $totalSteps = WorkflowStep::count();

            if ($latestLog == null) {
                return 'Processing';
            }

            if ($latestStep == $totalSteps) {
                return 'Completed';
            }

            return 'Processing';
        }

        return $status;
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
