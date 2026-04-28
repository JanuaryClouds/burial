<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function claimant()
    {
        return $this->hasOne(Claimant::class, 'burial_assistance_id', 'id')->latestOfMany('created_at');
    }

    public function trackingNumber()
    {
        return $this->originalClaimant()?->client?->tracking_no;
    }

    public function claimants()
    {
        return $this->hasMany(Claimant::class, 'burial_assistance_id', 'id');
    }

    public function hasPendingClaimantChange()
    {
        return $this->claimantChanges()->where('status', 'pending')->exists();
    }

    public function hasApprovedClaimantChange()
    {
        return $this->claimantChanges()->where('status', 'approved')->exists();
    }

    public function hasRejectedClaimantChange()
    {
        return $this->claimantChanges()->where('status', 'rejected')->exists();
    }

    public function claimantChanges()
    {
        return $this->hasMany(ClaimantChange::class, 'burial_assistance_id', 'id');
    }

    public function originalClaimant()
    {
        if (! $this->claimantChanges->count()) {
            return $this->claimant;
        }

        return $this->claimantChanges->first()->oldClaimant;
    }

    public function newClaimant()
    {
        return $this->claimantChanges()->first()->newClaimant;
    }

    public function currentClaimant()
    {
        if ($this->hasApprovedClaimantChange()) {
            return $this->newClaimant();
        }

        return $this->originalClaimant();
    }

    public function beneficiary()
    {
        return $this->originalClaimant()->client->beneficiary;
    }

    public function processLogs()
    {
        return $this->hasMany(ProcessLog::class, 'burial_assistance_id', 'id');
    }

    public function tracker()
    {
        return $this->hasOne(TrackingCode::class, 'trackable_id', 'id');
    }

    public function cheque()
    {
        return $this->hasMany(Cheque::class, 'burial_assistance_id', 'id');
    }

    public function latestCheque()
    {
        return $this->hasOne(Cheque::class, 'burial_assistance_id', 'id')->latestOfMany();
    }

    public function encoder()
    {
        return $this->belongsTo(User::class, 'encoder', 'id');
    }

    public function initialChecker()
    {
        return $this->belongsTo(User::class, 'initial_checker', 'id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id');
    }

    public function rejection()
    {
        return $this->hasOne(Rejection::class, 'burial_assistance_id', 'id');
    }

    // Scopes
    public function scopeAccessibleByUser($query, $userId)
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
        if (! auth()->user()) {
            return $query->whereRaw('1 = 0');
        }

        if (! auth()->user()) {
            return $query->whereRaw('1 = 0');
        }

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
