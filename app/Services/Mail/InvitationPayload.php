<?php

namespace App\Services\Mail;

final readonly class InvitationPayload implements MessagePayload
{
    public function __construct(public int $invitationId) {}

    public function type(): string
    {
        return 'user_invitation';
    }

    public function toArray(): array
    {
        return ['invitation_id' => $this->invitationId];
    }
}
