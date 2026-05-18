<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use LogicException;

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
     * @return HasOne<Claimant>
     */
    public function claimant(): HasOne
    {
        return $this->hasOne(Claimant::class, 'burial_assistance_id', 'id')->latestOfMany('created_at');
    }

    /**
     * Summary of trackingNumber
     * @return string Tracking number of the client
     */
    public function trackingNumber(): ?string
    {
        return $this->originalClaimant()->client->tracking_no;
    }

    /**
     * Summary of claimants
     * @return HasMany<Claimant>
     */
    public function claimants(): HasMany
    {
        return $this->hasMany(Claimant::class, 'burial_assistance_id', 'id');
    }

    /**
     * Summary of hasPendingClaimantChange
     * @return bool checks if the burial assistance has a pending claimant change
     */
    public function hasPendingClaimantChange(): bool
    {
        return $this->claimantChanges()->where('status', 'pending')->exists();
    }

    /**
     * Summary of hasApprovedClaimantChange
     * @return bool checks if the burial assistance has an approved claimant change
     */
    public function hasApprovedClaimantChange(): bool
    {
        return $this->claimantChanges()->where('status', 'approved')->exists();
    }

    /**
     * Summary of hasRejectedClaimantChange
     * @return bool checks if the burial assistance has a rejected claimant change
     */
    public function hasRejectedClaimantChange(): bool
    {
        return $this->claimantChanges()->where('status', 'rejected')->exists();
    }

    /**
     * Summary of claimantChanges
     * @return HasMany<ClaimantChange>
     */
    public function claimantChanges(): HasMany
    {
        return $this->hasMany(ClaimantChange::class, 'burial_assistance_id', 'id');
    }

    /**
     * Summary of originalClaimant
     * @return Claimant returns the original claimant of the burial assistance, or the current claimant if there is no claimant change
     */
    public function originalClaimant(): Claimant
    {
        if (! $this->claimantChanges->count()) {
            return $this->claimant ?? throw new LogicException('Claimant not found');
        }

        return $this->claimantChanges->first()->oldClaimant ?? throw new LogicException('Claimant not found');
    }

    /**
     * Summary of newClaimant
     * @return Claimant returns the new claimant of the burial assistance
     */
    public function newClaimant(): ?Claimant
    {
        return $this->claimantChanges()->first()->newClaimant;
    }

    /**
     * Summary of currentClaimant
     * @return Claimant returns the current claimant of the burial assistance
     */
    public function currentClaimant(): Claimant
    {
        if ($this->hasApprovedClaimantChange()) {
            return $this->newClaimant();
        }

        return $this->originalClaimant();
    }

    /**
     * Summary of beneficiary
     * @return Beneficiary returns the beneficiary of the burial assistance
     */
    public function beneficiary(): Beneficiary
    {
        return $this->originalClaimant()->client->beneficiary ?? throw new LogicException('Beneficiary not found');
    }

    /**
     * Summary of processLogs
     * @return HasMany<ProcessLog>
     */
    public function processLogs(): HasMany
    {
        return $this->hasMany(ProcessLog::class, 'burial_assistance_id', 'id');
    }

    /**
     * Summary of cheque
     * @return HasMany<Cheque>
     */
    public function cheque(): HasMany
    {
        return $this->hasMany(Cheque::class, 'burial_assistance_id', 'id');
    }

    /**
     * Summary of latestCheque
     * @return HasOne<Cheque> returns the latest cheque created
     */
    public function latestCheque(): HasOne
    {
        return $this->hasOne(Cheque::class, 'burial_assistance_id', 'id')->latestOfMany();
    }

    /**
     * Summary of encoder
     * @return BelongsTo<User, BurialAssistance>
     */
    public function encoder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encoder', 'id');
    }

    /**
     * Summary of initialChecker
     * @return BelongsTo<User, BurialAssistance>
     */
    public function initialChecker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initial_checker', 'id');
    }

    /**
     * Summary of rejection
     * @return HasOne<Rejection>
     */
    public function rejection(): HasOne
    {
        return $this->hasOne(Rejection::class, 'burial_assistance_id', 'id');
    }

    // Scopes
    /**
     * Summary of scopeAccessibleByUser
     * @param mixed $query
     * @param mixed $userId Filter to only show to one user
     * @return Builder
     */
    public function scopeAccessibleByUser($query, $userId): Builder
    {
        return $query->where(function ($q) use ($userId) {
            // CASE A: No claimant change requested
            $q->whereDoesntHave('claimantChanges', function () {})
                ->whereHas('claimant.client.user', function ($subQ) use ($userId) {
                    $subQ->where('id', $userId);
                });
        })
            ->orWhere(function ($q) use ($userId) {
                // CASE B: Claimant change requested
                $q->whereHas('claimantChanges', function ($cc) use ($userId) {
                    $cc->where(function ($ccInner) use ($userId) {
                        // APPROVED → both old and new claimant
                        $ccInner->where('status', 'approved')
                            ->where(function ($approvedUsers) use ($userId) {
                                $approvedUsers
                                    ->whereHas('oldClaimant.client.user', function ($q) use ($userId) {
                                        $q->where('id', $userId);
                                    })
                                    ->orWhereHas('newUserClaimant', function ($q) use ($userId) {
                                        $q->where('id', $userId);
                                    });
                            });
                    })->orWhere(function ($ccInner) use ($userId) {
                        // PENDING / REJECTED → only original claimant
                        $ccInner->whereIn('status', ['pending', 'rejected'])
                            ->whereHas('oldClaimant.client.user', function ($q) use ($userId) {
                                $q->where('id', $userId);
                            });
                    });
                });
            });
    }

    /**
     * Summary of scopeTotal
     * @param mixed $query
     * @return Builder
     */
    public function scopeTotal($query): Builder
    {
        if (! auth()->user()) {
            return $query->whereRaw('1 = 0'); // Return empty result for unauthenticated
        }

        if (auth()->user()->roles()->count() > 0) {
            return $query;
        }

        return $query->accessibleByUser(auth()->user()->id);
    }

    /**
     * Summary of scopePending
     * @param mixed $query
     * @return Builder
     */
    public function scopePending($query): Builder
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

    /**
     * Summary of scopeProcessing
     * @param mixed $query
     * @return Builder
     */
    public function scopeProcessing($query): Builder
    {
        if (! auth()->user()) {
            return $query->whereRaw('1 = 0');
        }

        if (auth()->user()->roles()->count() > 0) {
            return $query->whereIn('status', ['processing', 'approved']);
        }

        return $query->accessibleByUser(auth()->user()->id)
            ->whereIn('status', ['processing', 'approved']);
    }

    /**
     * Summary of scopeReleased
     * @param mixed $query
     * @return Builder
     */
    public function scopeReleased($query): Builder
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
