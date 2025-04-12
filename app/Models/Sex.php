<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sex extends Model
{
    use HasFactory;

    protected $table = 'sexes';
    protected $fillable = [
        'name',
        'remarks',
    ];

    public static function getAllSexes()
    {
        return self::all();
    }

    public function clientDemographic()
    {
        return $this->hasMany(ClientDemographic::class);    
    }
}