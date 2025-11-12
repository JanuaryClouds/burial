<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientBeneficiary extends Model
{
    use HasFactory;
    protected $table = 'client_beneficiaries';

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

    public static function getClientBeneficiary($client)
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