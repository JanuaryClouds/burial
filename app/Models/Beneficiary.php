<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class Beneficiary extends Model
{
    use HasFactory;

    protected $table = 'beneficiaries';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'client_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'sex_id',
        'religion_id',
        'date_of_birth',
        'date_of_death',
        'place_of_birth',
        'barangay_id',
    ];

    protected $casts = [
        'first_name' => 'encrypted',
        'middle_name' => 'encrypted',
        'last_name' => 'encrypted',
        'suffix' => 'encrypted',
    ];

    public function tracking_no()
    {
        return $this->client?->tracking_no;
    }

    public function fullname()
    {
        return $this->first_name.' '.
            ($this->middle_name ? Str::substr($this->middle_name, 0, 1).'. ' : '').
            $this->last_name.
            ($this->suffix ? ' '.$this->suffix : '');
    }

    public function age()
    {
        return Carbon::parse($this->created_at)->diffInYears($this->date_of_birth);
    }

    public function assistance()
    {
        if ($this->client?->claimant?->count() > 0) {
            return $this->client?->claimant?->burialAssistance;
        }

        if ($this->client?->funeralAssistance?->count() > 0) {
            return $this->client?->funeralAssistance;
        }

        return null;
    }

    public static function getBeneficiary($client)
    {
        return self::where('client_id', $client)->first();
    }

    public function sex()
    {
        return $this->belongsTo(Sex::class, 'sex_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class, 'religion_id');
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }
}
