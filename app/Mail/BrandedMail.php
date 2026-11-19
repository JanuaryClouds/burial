<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

abstract class BrandedMail extends Mailable
{
    public function brand(): array
    {
        return ['systemName' => config('app.name'), 'logoPath' => public_path('images/CSWDO-mail.png')];
    }
}
