<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FuneralAssistanceType extends Model
{
    /** @use HasFactory<\Database\Factories\FuneralAssistanceTypeFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    protected $table = 'funeral_assistance_types';

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Summary of recommendations
     * @return BelongsToMany<Recommendation, FuneralAssistanceType>
     */
    public function recommendations(): BelongsToMany
    {
        return $this->belongsToMany(Recommendation::class, 'recommendation_has_assistances', 'funeral_assistance_uuid', 'recommendation_uuid');
    }
}
