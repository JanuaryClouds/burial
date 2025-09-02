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
        "requires_extra_data",
        "is_optional",
        "extra_data_schema"
    ];
    protected $table = "workflow_steps";
}
