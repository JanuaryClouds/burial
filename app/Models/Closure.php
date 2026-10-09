<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Closure extends Model
{
    use HasUuid, HasFactory, SoftDeletes;

    protected $fillable = [
        'application_uuid',
        'closed_at',
        'closed_by',
        'reason',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_uuid');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
