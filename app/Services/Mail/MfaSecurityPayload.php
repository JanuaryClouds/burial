<?php

namespace App\Services\Mail;

final readonly class MfaSecurityPayload implements MessagePayload
{
    public function __construct(public int $userId, public string $event) {}

    public function type(): string
    {
        return 'mfa_security';
    }

    public function toArray(): array
    {
        return ['user_id' => $this->userId, 'event' => $this->event, 'occurred_at' => now()->toIso8601String()];
    }
}
