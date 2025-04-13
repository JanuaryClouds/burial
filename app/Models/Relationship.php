<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relationship extends Model
{
    use HasFactory;
    protected $table = 'relationships';
    protected $fillable = [
        'name',
        'remarks',
    ];

    public static function getAllRelationships()
    {
        return self::all();
    }
    
    public function clientSocialInfo()
    {
        return $this->hasMany(ClientSocialInfo::class);
    }

    public function clientFamily()
    {
        return $this->belongsTo(ClientBeneficiaryFamily::class);
    }
}