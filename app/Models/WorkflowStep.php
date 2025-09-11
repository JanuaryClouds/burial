<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowStep extends Model
{
    use HasFactory;
    protected $fillable = [
        "order_no",
        "handler_id",
        "description",
        "requires_extra_data",
        "is_optional",
        "extra_data_schema"
    ];

    protected $casts = [
        'extra_data_schema' => 'array'
    ];
    protected $table = "workflow_steps";

    public function processLog() {
        return $this->hasMany(ProcessLog::class, 'workflow_step_id', 'id');
    }

    public function handler() {
        return $this->hasOne(Handler::class, 'handler_id', 'id');
    }
}
