<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginService
{
    public function key(string $email): string
    {
        return 'staff-login.'.hash('sha256', mb_strtolower(trim($email)));
    }

    public function clear(string $email): void
    {
        $key = $this->key($email);
        Cache::lock($key.'.lock', 30)->block(5, fn () => Cache::forget($key));
    }

    /** @return array{redirect?: string, message?: string, failures?: int, retryAfter?: int} */
    public function attempt(#[\SensitiveParameter] array $data): array
    {
        $data['email'] = mb_strtolower(trim($data['email'] ?? ''));
        Validator::make($data, ['email' => 'required|email|max:254', 'password' => 'required|string'])->validate();
        $key = $this->key($data['email']);

        return Cache::lock($key.'.lock', 30)->block(5, function () use ($key, $data) {
            $now = now()->timestamp;
            $state = Cache::get($key, []);
            if (($state['until'] ?? 0) > $now) {
                return $this->failure($state['failures'], $state['until'] - $now);
            }
            if (($state['until'] ?? 0) || ($state['expires'] ?? 0) <= $now) {
                $state = ['failures' => 0, 'expires' => $now + (int) config('security.login.window_seconds', 60)];
            }

            $user = User::with('roles')->whereRaw('LOWER(email) = ?', [$data['email']])->first();
            // Perform password hashing even for unknown addresses to reduce timing differences.
            $valid = Hash::check($data['password'], $user?->getAuthPassword() ?? '$2y$12$VKC4HStrPoFtcSnL3TjfIee75Bgz/.fmhXPE0wjPXKqHzOOPTXLQi');
            if (! $valid || ! $user || ! $user->is_active || filled($user->citizen_uuid) || ! app(AccountAccess::class)->isInternal($user)) {
                $state['failures']++;
                $retry = 0;
                if ($state['failures'] >= (int) config('security.login.max_failures', 5)) {
                    $retry = (int) config('security.login.cooldown_seconds', 60);
                    $state['until'] = $now + $retry;
                }
                Cache::put($key, $state, max(1, ($state['until'] ?? $state['expires']) - $now));

                return $this->failure($state['failures'], $retry);
            }

            Auth::getProvider()->rehashPasswordIfRequired($user, $data);
            Auth::login($user);
            session()->regenerate();
            session(['account.staff_hash' => $user->getAuthPassword()]);
            if (! $user->password_change_required) {
                session(['api_token' => $user->createToken('fileserver')->plainTextToken]);
            }
            Cache::forget($key);

            return ['redirect' => route($user->password_change_required ? 'user.password' : 'dashboard')];
        });
    }

    private function failure(int $failures, int $retry): array
    {
        return ['failures' => $failures, 'retryAfter' => $retry,
            'message' => $retry > 0
                ? "Too many failed attempts. Try again in {$retry} seconds, or reset your password."
                : 'Invalid email or password.'];
    }
}
