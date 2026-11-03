<?php

namespace App\Services\Mail;

use App\Mail\PasswordRecoveryMail;
use App\Models\MailDelivery;
use App\Models\User;
use App\Services\Auth\AccountAccess;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

class PasswordResetMessage
{
    public static function version(User $user): string
    {
        return hash_hmac('sha256', $user->getAuthPassword(), config('app.key'));
    }

    public function prepare(MailDelivery $delivery): PasswordRecoveryMail
    {
        $token = DB::transaction(function () use ($delivery) {
            $payload = $delivery->payload;
            $user = User::with('roles')->whereKey($payload['user_id'])->lockForUpdate()->first();
            if (! $user || ! $user->is_active || ! app(AccountAccess::class)->isInternal($user)
                || $user->email !== $delivery->recipient || ! hash_equals(self::version($user), $payload['password_version'])) {
                throw new IneligibleMessage;
            }
            if (! Password::broker()->tokenExists($user, $payload['token'])) {
                $payload['token'] = Password::broker()->createToken($user);
                $delivery->payload = $payload;
                $delivery->save();
            }

            return $payload['token'];
        });
        $base = rtrim(config('app.url'), '/');
        if (! filter_var($base, FILTER_VALIDATE_URL) || (app()->isProduction() && ! str_starts_with($base, 'https://'))) {
            throw new \LogicException('Invalid mail URL configuration.');
        }
        $url = $base.'/admin/reset-password/'.rawurlencode($token).'?'.http_build_query(['email' => $delivery->recipient]);

        return new PasswordRecoveryMail($url, (int) config('auth.passwords.users.expire', 60));
    }
}
