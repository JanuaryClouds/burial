<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cheque extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'application_uuid', 'obr_number', 'cheque_number', 'dv_number',
        'amount', 'date_issued', 'date_claimed', 'status',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_uuid', 'uuid');
    }
}
