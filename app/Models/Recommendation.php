<?php

namespace App\Models;

use App\Traits\HasUuid;
use Database\Factories\RecommendationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Recommendation extends Model
{
    /** @use HasFactory<RecommendationFactory> */
    use HasFactory, HasUuid;

    protected $table = 'recommendations';

    protected $fillable = [
        'application_uuid',
        'amount_extended',
        'mode_of_assistance_id',
        'recommended_by',
        'status',
        'approved_at',
    ];

    /**
     * Summary of application
     *
     * @return BelongsTo<Application, Recommendation>
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_uuid', 'uuid');
    }

    /**
     * Summary of modeOfAssistance
     *
     * @return BelongsTo<ModeOfAssistance, Recommendation>
     */
    public function modeOfAssistance(): BelongsTo
    {
        return $this->belongsTo(ModeOfAssistance::class, 'mode_of_assistance_id', 'id');
    }

    /**
     * Summary of recommendedBy
     *
     * @return BelongsTo<User, Recommendation>
     */
    public function recommendedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recommended_by', 'id');
    }

    /**
     * Summary of assistance
     *
     * @return BelongsToMany<FuneralAssistanceType, Recommendation>
     */
    public function assistance(): BelongsToMany
    {
        return $this->belongsToMany(FuneralAssistanceType::class, 'recommendation_has_assistances', 'recommendation_uuid', 'funeral_assistance_uuid');
    }

    public static function relations(): array
    {
        return [
            'application',
            'modeOfAssistance',
            'recommendedBy',
            'assistance',
        ];
    }
}
