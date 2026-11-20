<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Maintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isMaintenance = SystemSetting::first()?->maintenance_mode ?? false;

        if ($isMaintenance) {
            $user = auth()->user();
            if ($user && $user->hasRole('superadmin')) {
                return $next($request);
            }

            // The public sign-in form submits through Livewire. Only the login
            // component may pass this maintenance exception; Livewire still
            // verifies the signed snapshot and allowed actions downstream.
            $components = $request->input('components');
            if ($request->isMethod('post') && app(\Livewire\Mechanisms\HandleRequests\HandleRequests::class)->isLivewireRoute()
                && is_array($components) && count($components) > 0
                && collect($components)->every(fn ($component) => is_array($component)
                    && is_string($component['snapshot'] ?? null)
                    && data_get(json_decode($component['snapshot'], true), 'memo.name') === 'auth.login')) {
                return $next($request);
            }

            if ($request->routeIs([
                'landing.page',
                'login',
                'login.check',
                'admin.login',
                'mfa.show',
                'mfa.submit',
                'sso',
                'sso.logout',
            ])) {
                return $next($request);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'System is currently under maintenance.',
                ], 503);
            }

            Auth::logout();

            return response()->view('error.maintenance', [], 503);
        }

        return $next($request);
    }
}
