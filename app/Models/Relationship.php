<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    /**
     * Summary of clientSocialInfo
     * @return HasMany<ClientSocialInfo>
     */
    public function clientSocialInfo(): HasMany
    {
        return $this->hasMany(ClientSocialInfo::class);
    }

    /**
     * Summary of beneficiaryFamilies
     * @return HasMany<BeneficiaryFamily>
     */
    public function beneficiaryFamilies(): HasMany
    {
        return $this->hasMany(BeneficiaryFamily::class, 'relationship_id', 'id');
    }

    /**
     * Summary of claimant
     * @return HasMany<Claimant>
     */
    public function claimant(): HasMany
    {
        return $this->hasMany(Claimant::class, 'relationship_to_deceased', 'id');
    }
}
