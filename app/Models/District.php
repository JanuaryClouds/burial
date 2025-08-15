<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;
    protected $table = "districts";
    protected $fillable = [
        'name',
        'remarks',
    ];

    public static function getAllDistricts()
    {
        return self::all();
    }

    public function client()
    {
        return $this->hasMany(Client::class);
    }

    public function barangay()
    {
        return $this->hasMany(Barangay::class);
    }
}