<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'burial_assistance_id',
        'claimant_id',
        'loggable_id',
        'loggable_type',
        'is_progress_step',
        'date_in',
        'date_out',
        'comments',
        'extra_data',
        'added_by',
    ];

    protected $casts = [
        'extra_data' => 'array',
    ];

    protected $table = 'process_logs';

    public function burialAssistance() {
        return $this->belongsTo(BurialAssistance::class, 'burial_assistance_id', 'id');
    }

    // ! No longer used in replacement of Loggable
    public function workflowStep() {
        return $this->belongsTo(WorkflowStep::class);
    }

    public function claimant() {
        return $this->belongsTo(Claimant::class, 'claimant_id', 'id');
    }

    public function loggable() {
        return $this->morphTo();
    }
}
