<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailDeliveryAttempt extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $hidden = ['claim'];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'finished_at' => 'datetime'];
    }
}
