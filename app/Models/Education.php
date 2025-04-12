<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;
    protected $table = "educations";
    protected $fillable = [
        'name',
        'remarks',
    ];

    public static function getAllEducations()
    {
        return self::all();
    }

    public function clientSocialInfo()
    {
        return $this->hasMany(ClientSocialInfo::class);
    }
}