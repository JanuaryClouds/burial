<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CivilStatus extends Model
{
    use HasFactory;
    protected $table = 'civil_statuses';
    protected $fillable = [
        'name',
        'remarks',
    ];

    public static function getAllCivilStatuses()
    {
        return self::all();
    }
}