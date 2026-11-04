<?php

namespace App\Services\Mail;

final class InvalidMailRecipient extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('A valid recipient email address is required.');
    }
}
