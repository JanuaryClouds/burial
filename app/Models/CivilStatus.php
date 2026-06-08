<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Summary of clientSocialInfo
     * @return HasMany<ClientSocialInfo>
     */
    public function clientSocialInfo(): HasMany
    {
        return $this->hasMany(ClientSocialInfo::class);
    }

    /**
     * Summary of clientFamily
     * @return HasMany<BeneficiaryFamily>
     */
    public function clientFamily(): HasMany
    {
        return $this->hasMany(BeneficiaryFamily::class);
    }
}
