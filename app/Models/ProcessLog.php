<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'burial_assistance_id',
        'workflow_step_id',
        'date_in',
        'date_out',
        'comments',
        'extra_data',
    ];
    protected $table = 'process_logs';
}
