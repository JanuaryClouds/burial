<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property Carbon $claimed_at
 */
class InterviewNotificationAttempt extends Model
{
    use HasUuid;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['claimed_at' => 'datetime'];
    }
}
