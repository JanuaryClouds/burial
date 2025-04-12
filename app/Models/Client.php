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

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }
}