<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimantChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'burial_assistance_id',
        'old_claimant_id',
        'new_claimant_id',
        'changed_at',
        'status',
        'reason_for_change',
        'new_claimant_user_id',
    ];

    protected $table = 'claimant_changes';

    /**
     * Summary of burialAssistance
     * @return BelongsTo<BurialAssistance, ClaimantChange>
     */
    public function burialAssistance(): BelongsTo
    {
        return $this->belongsTo(BurialAssistance::class, 'burial_assistance_id', 'id');
    }

    /**
     * Summary of oldClaimant
     * @return BelongsTo<Claimant, ClaimantChange>
     */
    public function oldClaimant(): BelongsTo
    {
        return $this->belongsTo(Claimant::class, 'old_claimant_id', 'id');
    }

    /**
     * Summary of newClaimant
     * @return BelongsTo<Claimant, ClaimantChange>
     */
    public function newClaimant(): BelongsTo
    {
        return $this->belongsTo(Claimant::class, 'new_claimant_id', 'id');
    }

    /**
     * Summary of newUserClaimant
     * @return BelongsTo<User, ClaimantChange>
     */
    public function newUserClaimant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'new_claimant_user_id', 'id');
    }
}
