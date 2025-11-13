<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $table = 'clients';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'tracking_no',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'age',
        'date_of_birth',
        'house_no',
        'street',
        'district_id',
        'barangay_id',
        'city',
        'contact_no'
    ];

    protected static function booted() {
        static::creating(function ($client) {
            $year = now()->format('Y');
            $count = self::whereYear('created_at', $year)->count() + 1;
    
            $client->tracking_no = sprintf('%s-%04d', $year, $count);
        });
    }

    public static function getAllClients()
    {
        return self::all();
    }

    public static function getClientInfo($client)
    {
        return self::where('id', $client)->first();
    }

    public function assessment()
    {
        return $this->hasMany(ClientAssessment::class);
    }

    public function beneficiary()
    {
        return $this->hasOne(ClientBeneficiary::class);
    }

    public function family()
    {
        return $this->hasMany(ClientBeneficiaryFamily::class);
    }

    public function demographic()
    {
        return $this->hasOne(ClientDemographic::class);
    }

    public function socialInfo()
    {
        return $this->hasOne(ClientSocialInfo::class);
    }

    public function recommendation()
    {
        return $this->hasMany(ClientRecommendation::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id', 'id');
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    public function claimant() {
        return $this->hasOne(Claimant::class, 'client_id', 'id');
    }
}