<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Education extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'educations';

    protected $fillable = [
        'name',
        'remarks',
    ];

    public static function getAllEducations()
    {
        return self::all();
    }

    /**
     * Summary of clientSocialInfo
     *
     * @return HasMany<ClientSocialInfo>
     */
    public function clientSocialInfo(): HasMany
    {
        return $this->hasMany(ClientSocialInfo::class);
    }
}
