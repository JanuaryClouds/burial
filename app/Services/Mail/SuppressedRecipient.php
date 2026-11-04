<?php

namespace App\Services\Mail;

final class SuppressedRecipient extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('The recipient is suppressed. Correct the email address and start a new request.');
    }
}
