<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assistance extends Model
{
    use HasFactory;

    protected $table = 'assistances';

    protected $fillable = [
        'name',
        'remarks',
    ];

    public static function getAllAssistances()
    {
        return self::all();
    }

    public static function getBurial()
    {
        return self::where('name', 'Burial');
    }

    /**
     * Summary of clientRecommendation
     *
     * @return HasMany<ClientRecommendation>
     */
    public function clientRecommendation(): HasMany
    {
        return $this->hasMany(ClientRecommendation::class);
    }
}
