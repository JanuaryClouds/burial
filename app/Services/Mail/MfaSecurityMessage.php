<?php

namespace App\Services\Mail;

use App\Mail\MfaSecurityMail;
use App\Models\MailDelivery;
use App\Models\User;

class MfaSecurityMessage
{
    public function prepare(MailDelivery $delivery): MfaSecurityMail
    {
        $owner = User::find($delivery->payload['user_id'] ?? 0);
        if (! $owner || mb_strtolower($owner->email) !== mb_strtolower($delivery->recipient)) {
            throw new IneligibleMessage;
        }

        return new MfaSecurityMail($delivery->payload['event'], $delivery->payload['occurred_at']);
    }
}
