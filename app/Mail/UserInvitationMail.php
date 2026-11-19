<?php

namespace App\Mail;

class UserInvitationMail extends BrandedMail
{
    public function __construct(public string $invitationUrl, public string $expiresAt) {}

    public function build(): static
    {
        return $this->subject('Your staff invitation — '.config('app.name'))->view('mail.invitation')->text('mail.invitation-text')->with($this->brand());
    }
}
