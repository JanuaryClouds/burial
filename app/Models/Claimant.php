<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claimant extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'relationship_to_deceased',
        'mobile_number',
        'address',
        'barangay_id',
    ];
    protected $table = "claimants";

    public function burialAssistance() {
        return $this->hasMany(BurialAssistance::class, 'claimant_id', 'id');
    }

    public function relationship() {
        return $this->belongsTo(Relationship::class, 'relationship_to_deceased', 'id');
    }

    public function oldClaimantChanges() {
        return $this->hasMany(ClaimantChange::class, 'old_claimant_id','id');
    }

    public function newClaimantChanges() {
        return $this->hasMany(ClaimantChange::class, 'new_claimant_id','id');
    }

    public function barangay() {
        return $this->belongsTo(Barangay::class, 'barangay_id', 'id');
    }

    public function processLogs() {
        return $this->hasMany(ProcessLog::class, 'claimant_id', 'id');
    }

    public function cheque() {
        return $this->hasOne(Cheque::class, 'claimant_id', 'id')->latestOfMany();
    }
}
