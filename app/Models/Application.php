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
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    /**
     * @return BelongsTo<Beneficiary, Application>
     */
    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id', 'id');
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
     * Summary of processLogs
     * @return HasMany<ProcessLog>
     */
    public function processLogs(): HasMany
    {
        return $this->hasMany(ProcessLog::class);
    }

    // Assistances
    /**
     * Summary of burialAssistance
     * @return HasOne<BurialAssistance, Application>
     */
    public function burialAssistance(): HasOne
    {
        return $this->hasOne(BurialAssistance::class);
    }

    /**
     * Summary of librengLibingAssistance
     * @return HasOne<LibrengLibingAssistance, Application>
     */
    public function librengLibingAssistance(): HasOne
    {
        return $this->hasOne(LibrengLibingAssistance::class);
    }

    /**
     * Summary of mortuaryAssistance
     * @return HasOne<MortuaryAssistance, Application>
     */
    public function mortuaryAssistance(): HasOne
    {
        return $this->hasOne(MortuaryAssistance::class);
    }

    /**
     * Summary of exhumationAssistance
     * @return HasOne<ExhumationAssistance, Application>
     */
    public function exhumationAssistance(): HasOne
    {
        return $this->hasOne(ExhumationAssistance::class);
    }

    /**
     * Summary of financialAssistance
     * @return HasOne<FinancialAssistance, Application>
     */
    public function financialAssistance(): HasOne
    {
        return $this->hasOne(FinancialAssistance::class);
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
