<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModeOfAssistance extends Model
{
    use HasFactory;
    protected $table = "mode_of_assistances";
    protected $fillable = [
        'name',
        'remarks',
    ];

    public static function getAllMoas()
    {
        return self::all();
    }
}