<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    use HasFactory;
    protected $table = "barangays";
    protected $fillables = [
        'name',
        'district_id',
        'remarks',
    ];

    public static function getAllBrangays()
    {
        return self::all();
    }
}