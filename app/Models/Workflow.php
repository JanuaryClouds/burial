<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workflow extends Model
{
    /** @use HasFactory<\Database\Factories\WorkflowFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    protected $table = "workflows";

    protected $fillable = [
        'name',
        'description'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Model Relationships.
    |
    */

    /**
     * Summary of stages
     * @return HasMany<WorkflowStage, Workflow>
     */
    public function stages(): HasMany
    {
        return $this->hasMany(WorkflowStage::class, 'workflow_uuid', 'uuid');
    }

    /**
     * Summary of transitions
     * @return HasMany<WorkflowTransition, Workflow>
     */
    public function transitions(): HasMany
    {
        return $this->hasMany(WorkflowTransition::class, 'workflow_uuid', 'uuid');
    }

    /**
     * Summary of funeralAssistanceTypes
     * @return HasMany<FuneralAssistanceType, Workflow>
     */
    public function funeralAssistanceTypes(): HasMany
    {
        return $this->hasMany(FuneralAssistanceType::class, 'workflow_uuid', 'uuid');
    }

    public static function relations(): array
    {
        return [
            'stages',
            'transitions',
            'funeralAssistanceTypes'
        ];
    }
}
