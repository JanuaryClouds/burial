<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WorkflowStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_no',
        'handler_id',
        'description',
        'requires_extra_data',
        'is_optional',
        'extra_data_schema',
    ];

    protected $casts = [
        'extra_data_schema' => 'array',
    ];

    protected $table = 'workflow_steps';

    // ! No longer used in replacement of Loggable
    public function processLog()
    {
        return $this->hasMany(ProcessLog::class);
    }

    /**
     * Summary of handler
     * @return BelongsTo<Handler, WorkflowStep>
     */
    public function handler(): BelongsTo
    {
        return $this->belongsTo(Handler::class, 'handler_id', 'id');
    }
}
