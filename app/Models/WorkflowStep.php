<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowStep extends Model
{
    use HasFactory, HasUuid;
    
    protected $fillable = [
        'order_no',
        'description',
    ];


    protected $table = 'workflow_steps';

}
