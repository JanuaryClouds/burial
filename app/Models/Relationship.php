<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    /**
     * Summary of beneficiaryFamilies
     *
     * @return HasMany<BeneficiaryFamily>
     */
    public function beneficiaryFamilies(): HasMany
    {
        return $this->hasMany(BeneficiaryFamily::class, 'relationship_id', 'id');
    }

    /**
     * Summary of applications
     *
     * @return HasMany<Application, Relationship>
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'relationship_id', 'id');
    }
}
