<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AccountSession
{
    public function start(User $user, Request $request): void
    {
        $token = Str::random(64);
        $user->newQuery()->whereKey($user->getKey())->update(['active_session_token' => hash('sha256', $token)]);
        $request->session()->put('account.session_token', $token);
    }

    public function valid(User $user, Request $request): bool
    {
        $token = $request->session()->get('account.session_token');
        $active = $user->newQuery()->whereKey($user->getKey())->value('active_session_token');

        // Enroll sessions created before this feature atomically. Once claimed,
        // another legacy session cannot take ownership from the active session.
        if ($active === null) {
            $token = Str::random(64);
            $user->newQuery()->whereKey($user->getKey())->whereNull('active_session_token')
                ->update(['active_session_token' => hash('sha256', $token)]);
            $request->session()->put('account.session_token', $token);
            $active = $user->newQuery()->whereKey($user->getKey())->value('active_session_token');
        }

        return is_string($token) && is_string($active) && hash_equals($active, hash('sha256', $token));
    }
}
