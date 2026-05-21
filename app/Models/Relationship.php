<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Relationship extends Model
{
    use HasFactory, SoftDeletes;

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
        return $this->belongsTo(BeneficiaryFamily::class);
    }

    public function claimant()
    {
        return $this->hasMany(Claimant::class, 'relationship_to_deceased', 'id');
    }
}
