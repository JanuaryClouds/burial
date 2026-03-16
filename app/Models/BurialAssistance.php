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
        return $this->claimant->client->tracking_no;
    }
    
    public function claimants()
    {
        return $this->hasMany(Claimant::class, 'burial_assistance_id', 'id');
    }

    public function claimantChanges()
    {
        return $this->hasMany(ClaimantChange::class, 'burial_assistance_id', 'id');
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
}
