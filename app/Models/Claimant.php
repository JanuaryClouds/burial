<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class Claimant extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'burial_assistance_id',
        'client_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'date_of_birth',
        'address',
        'barangay_id',
        'city',
        'contact_number',
        'relationship_id',
    ];

    protected $casts = [
        'first_name' => 'encrypted',
        'middle_name' => 'encrypted',
        'last_name' => 'encrypted',
        'suffix' => 'encrypted',
        'address' => 'encrypted',
        'contact_number' => 'encrypted',
    ];

    protected $table = 'claimants';

    public function fullname()
    {
        return $this->first_name.' '.
            ($this->middle_name ? Str::limit($this->middle_name, 1, '.').' ' : '').
            $this->last_name.
            ($this->suffix ? ' '.$this->suffix : '');
    }

    public function age()
    {
        return Carbon::parse($this->date_of_birth)->age;
    }

    public function fullAddress()
    {
        return $this->address.', '.$this->barangay?->name;
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function burialAssistance()
    {
        return $this->belongsTo(BurialAssistance::class, 'burial_assistance_id', 'id');
    }

    public function relationship()
    {
        return $this->belongsTo(Relationship::class, 'relationship_id', 'id');
    }

    public function oldClaimantChanges()
    {
        return $this->hasMany(ClaimantChange::class, 'old_claimant_id', 'id');
    }

    public function newClaimantChanges()
    {
        return $this->hasMany(ClaimantChange::class, 'new_claimant_id', 'id');
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id', 'id');
    }

    public function processLogs()
    {
        return $this->hasMany(ProcessLog::class, 'claimant_id', 'id');
    }

    public function cheque()
    {
        return $this->hasOne(Cheque::class, 'claimant_id', 'id')->latestOfMany();
    }
}
