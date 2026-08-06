<?php

namespace App\Models;

use App\Traits\HasUuid;
use Database\Factories\AssessmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Assessment extends Model
{
    /** @use HasFactory<AssessmentFactory> */
    use HasFactory, HasUuid;

    protected $table = 'assessments';

    protected $fillable = [
        'application_uuid',
        'swa',
        'problem_presented',
    ];

    protected static function booted()
    {
        static::creating(function ($assessment) {
            $assessment->uuid = (string) Str::uuid();
        });
    }

    /**
     * Summary of application
     *
     * @return BelongsTo<Application, Assessment>
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id', 'id');
    }
}
