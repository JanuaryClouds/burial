<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PasswordRecoveryMail extends Mailable
{
    public function __construct(public string $resetUrl, public int $expiresIn) {}

    public function build(): static
    {
        return $this->subject('Reset your Burial password')->view('mail.password-reset');
    }
}
