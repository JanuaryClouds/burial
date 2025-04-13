<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $table = 'clients';
    protected $fillable = [
        'tracking_no',
        'first_name',
        'middle_name',
        'last_name',
        'age',
        'date_of_birth',
        'house_no',
        'street',
        'district_id',
        'barangay_id',
        'city',
        'contact_no'
    ];

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
        return $this->hasMany(ClientBeneficiary::class);
    }

    public function family()
    {
        return $this->hasMany(ClientBeneficiaryFamily::class);
    }

    public function demographic()
    {
        return $this->hasMany(ClientDemographic::class);
    }

    public function socialInfo()
    {
        return $this->hasMany(ClientSocialInfo::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }
}