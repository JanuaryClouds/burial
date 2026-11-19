<?php

namespace App\Services\Mail;

use App\Mail\UserInvitationMail;
use App\Models\MailDelivery;
use App\Models\User;
use App\Models\UserInvitation;
use App\Services\Auth\InvitationService;
use Illuminate\Support\Facades\DB;

class InvitationMessage
{
    public function prepare(MailDelivery $delivery): UserInvitationMail
    {
        return DB::transaction(function () use ($delivery) {
            $row = UserInvitation::whereKey($delivery->user_invitation_id)->lockForUpdate()->first();
            if (! $row || ! $row->isPending() || ! $row->token || $row->email !== $delivery->recipient
                || User::whereRaw('LOWER(email) = ?', [$row->email])->exists()) {
                throw new IneligibleMessage;
            }
            if ($row->expires_at->lte(now())) {
                app(InvitationService::class)->renewLocked($row);
            }
            $url = MailUrl::base().'/admin/invitations/'.$row->token;

            return new UserInvitationMail($url, $row->expires_at->toIso8601String());
        });
    }
}
