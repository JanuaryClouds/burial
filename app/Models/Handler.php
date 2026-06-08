<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Handler extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'department',
        'is_active',
    ];

    protected $table = 'handlers';

    /**
     * Summary of workflow
     *
     * @return BelongsTo<WorkflowStep, Handler>
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'handler_id', 'id');
    }
}
