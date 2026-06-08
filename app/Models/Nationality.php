<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nationality extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'nationalities';

    protected $fillable = [
        'name',
        'remarks',
    ];

    public static function getAllNationalities()
    {
        return self::all();
    }

    /**
     * Summary of clientDemographics
     *
     * @return HasMany<ClientDemographic>
     */
    public function clientDemographics(): HasMany
    {
        return $this->hasMany(ClientDemographic::class);
    }
}
