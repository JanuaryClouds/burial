<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BurialAssistance extends Model
{
    use HasFactory;
    protected $fillable = [
        'tracking_no',
        'tracking_code',
        'application_date',
        'swa',
        'encoder',
        'funeraria',
        'deceased_id',
        'claimant_id',
        'amount',
        'status',
        'remarks',
        'initial_checker',
    ];

    protected static function booted() {
        static::creating(function ($burialAssistance) {
            $year = now()->format('Y');
            $count = self::whereYear('created_at', $year)->count() + 1;
    
            $burialAssistance->tracking_no = sprintf('%s-%04d', $year, $count);
        });
    }

    public function deceased() {
        return $this->belongsTo(Deceased::class, 'deceased_id', 'id');
    }

    public function claimant() {
        return $this->belongsTo(Claimant::class, 'claimant_id', 'id');
    }

    public function claimantChanges() {
        return $this->hasMany(ClaimantChange::class, 'burial_assistance_id', 'id');
    }

    public function processLogs() {
        return $this->hasMany(ProcessLog::class, 'burial_assistance_id', 'id');
    }

    // ! Unused
    public function cheque() {
        return $this->hasOne(Cheque::class, 'burial_assistance_id', 'id');
    }

    public function encoder() {
        return $this->belongsTo(User::class, 'encoder', 'id');
    }

    public function initialChecker() {
        return $this->belongsTo(User::class, 'initial_checker', 'id');
    }
}
