<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Religion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'religions';

    protected $fillable = [
        'name',
        'remarks',
    ];

    public static function getAllReligions()
    {
        return self::all();
    }

    public function clientDemographic()
    {
        return $this->hasMany(ClientDemographic::class);
    }

    public function beneficiary()
    {
        return $this->hasMany(Beneficiary::class, 'religion_id', 'id');
    }
}
