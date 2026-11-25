<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property Carbon $schedule
 * @property array{development: bool, user_id: int|null, citizen_uuid: string|null, phone: string, portal_message: string, sms_message: string, channels: list<string>} $snapshot
 */
class InterviewSchedulingRequest extends Model
{
    use HasUuid;

    protected $guarded = [];

    protected $hidden = ['snapshot'];

    protected function casts(): array
    {
        return ['snapshot' => 'encrypted:array', 'schedule' => 'datetime'];
    }
}
