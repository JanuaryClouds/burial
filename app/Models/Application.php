<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Application extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationFactory> */
    use HasFactory, HasUuid;

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
        });
    }

    /** 
     * Get the client that owns the application.
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
     * @return HasOne<Assessment>
     */
    public function assessment(): HasOne
    {
        return $this->hasOne(Assessment::class);
    }

    /**
     * Summary of recommendation
     * @return HasMany<Recommendation, Application>
     */
    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class, 'application_uuid', 'uuid');
    }

    /**
     * Summary of processLogs
     * @return HasMany<ProcessLog>
     */
    public function processLogs(): HasMany
    {
        return $this->hasMany(ProcessLog::class);
    }

    public function status(): String
    {
        $status = "Pending";

        if ($this->client->interviews->count() > 0) {
            if ($this->client->interviews->first()->status == 'done') {
                $status = "Interviewed";
            } else {
                $status = "Scheduled";
            }
        }

        if ($this->assessment) {
            $status = "Assessed";
        }

        if ($this->recommendations->count() > 0) {
            $recommendation = $this->recommendations->where('status', 'approved');

            if ($recommendation != null) {
                $status = "Recommended";
            }
        }

        if ($this->referral) {
            $status = "Referred";
        }

        if ($this->processLogs->count() > 0) {
            $latestLog = $this->processLogs()->first();
            $latestStep = $latestLog->loggable()->order_no;
            $totalSteps = WorkflowStep::count();
    
            if ($latestLog == null) {
                return "Processing";
            }
    
            if ($latestStep == $totalSteps) {
                return "Completed";
            }
    
            return "Processing";
        }

        return $status;
    }

    /**
     * Summary of referral
     * @return HasOne<Referral, Application>
     */
    public function referral(): HasOne
    {
        return $this->hasOne(Referral::class);
    }
}
