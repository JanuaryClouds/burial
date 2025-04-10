<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assistance extends Model
{
    use HasFactory;
    protected $table = "assistances";
    protected $fillable = [
        'name',
        'remarks',
    ];

    public static function getAllAssistances()
    {
        return self::all();
    }
}