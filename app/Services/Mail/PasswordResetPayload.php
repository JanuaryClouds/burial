<?php

namespace App\Services\Mail;

final readonly class PasswordResetPayload implements MessagePayload
{
    public function __construct(public int $userId, public string $passwordVersion, public string $token) {}

    public function type(): string
    {
        return 'password_reset';
    }

    public function toArray(): array
    {
        return ['user_id' => $this->userId, 'password_version' => $this->passwordVersion, 'token' => $this->token];
    }
}
