<?php

namespace App\Mail;

class PasswordRecoveryMail extends BrandedMail
{
    public function __construct(public string $resetUrl, public int $expiresIn) {}

    public function build(): static
    {
        return $this->subject('Reset your Burial password')->view('mail.password-reset')->text('mail.password-reset-text')->with($this->brand());
    }
}
