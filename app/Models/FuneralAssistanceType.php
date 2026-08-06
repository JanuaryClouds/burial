<?php

namespace App\Models;

use App\Traits\HasUuid;
use Database\Factories\FuneralAssistanceTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FuneralAssistanceType extends Model
{
    /** @use HasFactory<FuneralAssistanceTypeFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    protected $table = 'funeral_assistance_types';

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Summary of recommendations
     *
     * @return HasMany<Recommendation, FuneralAssistanceType>
     */
    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class, 'funeral_assistance_type_uuid', 'uuid');
    }
}
