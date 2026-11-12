<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\Mail\ImmediateMailService;
use App\Services\Mail\PasswordResetMessage;
use App\Services\Mail\PasswordResetPayload;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PasswordRecoveryService
{
    public function request(string $email): void
    {
        $delivery = DB::transaction(function () use ($email) {
            $user = User::with('roles')->where('email', $email)->lockForUpdate()->first();
            if (! $user || ! $user->is_active || ! app(AccountAccess::class)->isInternal($user)) {
                return null;
            }
            $delivery = null;
            Password::broker()->sendResetLink(['email' => $user->email], function ($account, $token) use ($user, &$delivery) {
                $delivery = app(ImmediateMailService::class)->record($user->email,
                    new PasswordResetPayload($user->id, PasswordResetMessage::version($user), $token));
            });

            return $delivery;
        });
        if ($delivery) {
            app(ImmediateMailService::class)->deliver($delivery);
        }
    }

    public function reset(array $data): void
    {
        Validator::make($data, ['email' => ['required', 'email', 'max:254'], 'token' => ['required', 'string', 'max:255'],
            'password' => [...PasswordPolicy::rules(), 'confirmed']])->validate();
        DB::transaction(function () use ($data) {
            $user = User::with('roles')->where('email', $data['email'])->lockForUpdate()->first();
            if (! $user || ! $user->is_active || ! app(AccountAccess::class)->isInternal($user)) {
                $this->invalid();
            }
            $result = Password::broker()->reset($data, function (User $account, string $password) {
                if (Hash::check($password, $account->password)) {
                    throw ValidationException::withMessages(['password' => 'Choose a password different from your current password.']);
                }
                $account->forceFill(['password' => $password, 'password_change_required' => false,
                    'remember_token' => Str::random(60)])->save();
                $account->tokens()->delete();
                activity('account-management')->causedBy($account)->performedOn($account)
                    ->withProperties(['method' => 'email_recovery'])
                    ->log('Owner reset password through email recovery');
                event(new PasswordReset($account));
            });
            if ($result !== Password::PASSWORD_RESET) {
                $this->invalid();
            }
        });
        app(LoginService::class)->clear($data['email']);
    }

    private function invalid(): never
    {
        throw ValidationException::withMessages(['email' => 'This password reset link is invalid or has expired. Please request a new link.']);
    }
}
