<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowHistory extends Model
{
    /** @use HasFactory<\Database\Factories\WorkflowHistoryFactory> */
    use HasFactory, HasUuid;

    protected $table = 'workflow_histories';

    protected $fillable = [
        'workflow_id',
        'from_stage_uuid',
        'to_stage_uuid',
        'date_in',
        'date_out',
        'reason',
        'processed_by',
        'application_uuid'
    ];
}
