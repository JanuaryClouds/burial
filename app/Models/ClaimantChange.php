<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];
    protected $table = 'claimant_changes';

    public function burialAssistance() {
        return $this->belongsTo(BurialAssistance::class, 'burial_assistance_id', 'id');
    }

    public function oldClaimant() {
        return $this->belongsTo(Claimant::class, 'old_claimant_id', 'id');
    }

    public function newClaimant() {
        return $this->belongsTo(Claimant::class, 'new_claimant_id', 'id');
    }
}
