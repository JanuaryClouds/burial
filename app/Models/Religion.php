<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    /**
     * Summary of clientDemographic
     * @return HasMany<ClientDemographic>
     */
    public function clientDemographic(): HasMany
    {
        return $this->hasMany(ClientDemographic::class);
    }

    /**
     * Summary of beneficiary
     * @return HasMany<Beneficiary>
     */
    public function beneficiary(): HasMany
    {
        return $this->hasMany(Beneficiary::class, 'religion_id', 'id');
    }
}
