<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowTransition extends Model
{
    /** @use HasFactory<\Database\Factories\WorkflowTransitionFactory> */
    use HasFactory, HasUuid;

    protected $table = 'workflow_transitions';

    protected $fillable = [
        'workflow_uuid',
        'from_stage_uuid',
        'to_stage_uuid',
        'permission_id',
    ];
}
