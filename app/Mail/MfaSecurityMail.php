<?php

namespace App\Mail;

class MfaSecurityMail extends BrandedMail
{
    public function __construct(public string $securityEvent, public string $occurredAt) {}

    public function build(): static
    {
        return $this->subject('Account security change — '.config('app.name'))->view('mail.mfa-security')->text('mail.mfa-security-text')->with($this->brand());
    }
}
