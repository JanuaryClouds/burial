<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    use HasFactory;

    protected $table = 'barangays';

    protected $fillable = [
        'name',
        'district_id',
        'remarks',
    ];

    public static function getAllBarangays()
    {
        return self::all();
    }

    public function client()
    {
        return $this->hasMany(Client::class, 'barangay_id', 'id');
    }

    public function beneficiary()
    {
        return $this->hasMany(Beneficiary::class, 'barangay_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function claimant()
    {
        return $this->hasMany(Claimant::class, 'barangay_id', 'id');
    }
}
