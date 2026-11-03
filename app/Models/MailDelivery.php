<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property array<string, mixed>|null $payload
 * @property Carbon|null $claimed_at
 * @property Carbon|null $sent_at
 */
class MailDelivery extends Model
{
    protected $guarded = [];

    protected $hidden = ['recipient', 'payload', 'claim'];

    protected function casts(): array
    {
        return ['recipient' => 'encrypted', 'payload' => 'encrypted:array', 'claimed_at' => 'datetime', 'sent_at' => 'datetime'];
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(MailDeliveryAttempt::class);
    }

    public function failureDescription(): string
    {
        return match ($this->failure_code) {
            'ineligible' => 'This recovery request is no longer eligible. Ask the user to request a new link.',
            'interrupted' => 'The outcome is uncertain. This email may already have been delivered. Retrying may send a duplicate.',
            'configuration' => 'Email configuration is unavailable or invalid.',
            default => 'The email could not be sent. Check the mail service before retrying.',
        };
    }
}
