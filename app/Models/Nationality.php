<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nationality extends Model
{
    use HasFactory;

    protected $table = 'nationalities';
    protected $fillable = [
        'name', 
        'remarks'
    ];

    public static function getAllNationalities()
    {
        return self::all();
    }

    public function clientDemongraphic()
    {
        return $this->hasMany(ClientDemographic::class);
    }
}