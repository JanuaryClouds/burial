<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Assessment extends Model
{
    /** @use HasFactory<\Database\Factories\AssessmentFactory> */
    use HasFactory, HasUuid;

    protected $table = "assessments";

    protected $fillable = [
        'application_id',
        'swa',
        'problem_presented',
        'amount_extended',
        'mode_of_assistance_id',
        'remarks'
    ];

    protected $casts = [
        'amount_extended' => 'encrypted',
    ];

    protected static function booted()
    {
        static::creating(function ($assessment) {
            $assessment->uuid = (string) Str::uuid();
        });
    }

    /**
     * Summary of application
     * @return BelongsTo<Application, Assessment>
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id', 'id');
    }

    /**
     * Summary of mode_of_assistance
     * @return BelongsTo<ModeOfAssistance, Assessment>
     */
    public function mode_of_assistance(): BelongsTo
    {
        return $this->belongsTo(ModeOfAssistance::class, 'mode_of_assistance_id', 'id');
    }
}
