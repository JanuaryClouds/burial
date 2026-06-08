<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Summary of clientDemographic
     * @return HasMany<ClientDemographic>
     */
    public function clientDemographic(): HasMany
    {
        return $this->hasMany(ClientDemographic::class);
    }

    /**
     * Summary of beneficiaryFamilies
     * @return HasMany<BeneficiaryFamily>
     */
    public function beneficiaryFamilies(): HasMany
    {
        return $this->hasMany(BeneficiaryFamily::class, 'sex_id', 'id');
    }
}
