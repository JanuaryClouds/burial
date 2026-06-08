<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BurialAssistance extends Model
{
    use HasFactory;

    protected $table = 'burial_assistances';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'application_date',
        'swa',
        'encoder',
        'funeraria',
        'amount',
        'status',
        'remarks',
        'initial_checker',
    ];

    /**
     * Summary of claimant
     *
     * @return HasOne<Claimant>
     */
    public function claimant(): HasOne
    {
        return $this->hasOne(Claimant::class, 'burial_assistance_id', 'id')->latestOfMany('created_at');
    }

    /**
     * Summary of trackingNumber
     *
     * @return string|null returns the tracking number from the client record
     */
    public function trackingNumber(): ?string
    {
        return $this->originalClaimant()->client->tracking_no;
    }

    /**
     * Summary of claimants
     *
     * @return HasMany<Claimant>
     */
    public function claimants(): HasMany
    {
        return $this->hasMany(Claimant::class, 'burial_assistance_id', 'id');
    }

    /**
     * Summary of hasClaimantChange
     *
     * @return bool returns if burial assistance's claimant has been changed
     */
    public function hasClaimantChange(): bool
    {
        return $this->claimantChanges()->exists();
    }

    /**
     * Summary of claimantChanges
     *
     * @return HasMany<ClaimantChange>
     */
    public function claimantChanges(): HasMany
    {
        return $this->hasMany(ClaimantChange::class, 'burial_assistance_id', 'id');
    }

    /**
     * Summary of originalClaimant
     *
     * @return Claimant returns the original claimant
     */
    public function originalClaimant(): Claimant
    {
        if (! $this->claimantChanges->count()) {
            return $this->claimant;
        }

        return $this->claimantChanges->first()->oldClaimant;
    }

    /**
     * Summary of newClaimant
     *
     * @return Claimant returns the new claimant if there's a claimant change
     */
    public function newClaimant(): ?Claimant
    {
        return $this->claimantChanges()->first()?->newClaimant;
    }

    /**
     * Summary of currentClaimant
     *
     * @return Claimant returns the current claimant (original if no changes, new if there's a change)
     */
    public function currentClaimant(): Claimant
    {
        if ($this->hasClaimantChange()) {
            return $this->newClaimant();
        }

        return $this->originalClaimant();
    }

    /**
     * Summary of beneficiary
     *
     * @return Beneficiary returns the beneficiary
     */
    public function beneficiary(): Beneficiary
    {
        return $this->originalClaimant()->client->beneficiary;
    }

    /**
     * Summary of processLogs
     *
     * @return HasMany<ProcessLog>
     */
    public function processLogs(): HasMany
    {
        return $this->hasMany(ProcessLog::class, 'burial_assistance_id', 'id');
    }

    /**
     * Summary of cheques
     *
     * @return HasMany<Cheque>
     */
    public function cheques(): HasMany
    {
        return $this->hasMany(Cheque::class, 'burial_assistance_id', 'id');
    }

    /**
     * Summary of latestCheque
     *
     * @return HasOne<Cheque>
     */
    public function latestCheque(): HasOne
    {
        return $this->hasOne(Cheque::class, 'burial_assistance_id', 'id')->latestOfMany();
    }

    /**
     * Summary of encoder
     *
     * @return BelongsTo<User, BurialAssistance>
     */
    public function encoder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encoder', 'id');
    }

    /**
     * Summary of initialChecker
     *
     * @return BelongsTo<User, BurialAssistance>
     */
    public function initialChecker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initial_checker', 'id');
    }

    // Scopes
    public function scopeAccessibleByUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            // CASE A: No claimant change requested
            $q->whereDoesntHave('claimantChanges')
                ->whereHas('claimant.client.user', function ($subQ) use ($userId) {
                    $subQ->where('id', $userId);
                });
        })
            ->orWhere(function ($q) use ($userId) {
                // CASE B: Claimant change requested (automatically approved)
                $q->whereHas('claimantChanges', function ($cc) use ($userId) {
                    $cc->where(function ($approvedUsers) use ($userId) {
                        $approvedUsers
                            ->whereHas('oldClaimant.client.user', function ($q) use ($userId) {
                                $q->where('id', $userId);
                            })
                            ->orWhereHas('newUserClaimant', function ($q) use ($userId) {
                                $q->where('id', $userId);
                            });
                    });
                });
            });
    }

    public function scopeTotal($query)
    {
        if (! auth()->user()) {
            return $query->whereRaw('1 = 0'); // Return empty result for unauthenticated
        }

        if (auth()->user()->roles()->count() > 0) {
            return $query;
        }

        return $query->accessibleByUser(auth()->user()->id);
    }

    public function scopePending($query)
    {
        if (! auth()->user()) {
            return $query->whereRaw('1 = 0');
        }

        if (auth()->user()->roles()->count() > 0) {
            return $query->where('status', 'pending');
        }

        return $query->accessibleByUser(auth()->user()->id)
            ->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        if (auth()->user()->roles()->count() > 0) {
            return $query->whereIn('status', ['processing', 'approved']);
        }

        return $query->accessibleByUser(auth()->user()->id)
            ->whereIn('status', ['processing', 'approved']);
    }

    public function scopeReleased($query)
    {
        if (! auth()->user()) {
            return $query->whereRaw('1 = 0');
        }

        if (auth()->user()->roles()->count() > 0) {
            return $query->where('status', 'released');
        }

        return $query->accessibleByUser(auth()->user()->id)
            ->where('status', 'released');
    }
}
