<?php

namespace App\Http\Middleware;

use App\Services\Auth\AccountAccess;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Livewire\Mechanisms\HandleRequests\HandleRequests;
use Symfony\Component\HttpFoundation\Response;

class CheckUserIfActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        $access = app(AccountAccess::class);
        $authenticatedUser = auth()->user();
        $accessToken = $authenticatedUser->currentAccessToken();
        $user = $access->freshForRequest($authenticatedUser);
        $internal = $user && $access->isInternal($user);
        $valid = $user && ($internal || $access->isCitizen($user));
        $blocked = ! $valid || ($internal && ! $access->isActive($user));
        // Only an authenticated Sanctum token may use token-specific handling.
        // An arbitrary Authorization header on a web session is not authentication.
        $tokenRequest = $accessToken instanceof PersonalAccessToken;

        // A token must never bypass local account status or first-login restrictions.
        $blocked = $blocked || ($tokenRequest && (! $access->isActive($user) || $access->needsPasswordChange($user)));
        if ($blocked) {
            Auth::forgetGuards();
            if ($request->hasSession()) {
                auth('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return $request->expectsJson()
                ? response()->json(['message' => 'Your account is inactive or unavailable. Please contact CSWDO.'], 403)
                : redirect()->route('admin.login')->with('warning', 'Your account is inactive or unavailable. Please contact CSWDO.');
        }
        // Preserve the guard's authenticated token (including cookie-session
        // TransientToken) while refreshing attributes and role assignments.
        $authenticatedUser->setRawAttributes($user->getAttributes(), true);
        $authenticatedUser->setRelations($user->getRelations());
        auth()->setUser($authenticatedUser);

        if ($internal && ! $tokenRequest && ! $request->routeIs('logout', 'sso.logout')) {
            $hash = $request->session()->get('account.staff_hash');
            if (! is_string($hash) || ! hash_equals($user->getAuthPassword(), $hash)) {
                auth('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return $request->expectsJson()
                    ? response()->json(['message' => 'Please sign in again.'], 401)
                    : redirect()->route('admin.login')->with('info', 'Please sign in again.');
            }
        }

        if ($access->needsPasswordChange($user) && ! $request->routeIs('user.password', 'logout', 'sso.logout')) {
            $livewire = app(HandleRequests::class)->isLivewireRoute();
            if (! $livewire) {
                return $request->expectsJson()
                    ? response()->json(['message' => 'Change your initial password before continuing.'], 403)
                    : redirect()->route('user.password');
            }
        }

        return $next($request);
    }
}
