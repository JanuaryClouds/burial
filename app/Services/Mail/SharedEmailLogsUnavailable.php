<?php

namespace App\Services\Mail;

final class SharedEmailLogsUnavailable extends \RuntimeException
{
    public function __construct()
    {
        // Never carry a connection exception or credentials into exception reporting.
        parent::__construct('Email suppression checks are unavailable. Sending was stopped.');
    }
}
