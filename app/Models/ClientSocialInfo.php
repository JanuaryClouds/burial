<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientSocialInfo extends Model
{
    use HasFactory;
    protected $table = 'client_social_infos';
    protected $fillable  = [
        'client_id',
        'relationship_id',
        'civil_id',
        'education_id', 
        'income',
        'philhealth', 
        'skill',
    ];

    public static function getClientSocial($client)
    {
        return self::where($client, 'client_id')->first();
    }

    public function relationship()
    {
        return $this->belongsTo(Relationship::class, 'relationship_id');
    }

    public function education()
    {
        return $this->belongsTo(Education::class, 'education_id');
    }

    public function civil()
    {
        return $this->belongsTo(CivilStatus::class, 'civil_id');
    }
}