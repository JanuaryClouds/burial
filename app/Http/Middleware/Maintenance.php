<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
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

            if ($request->routeIs([
                'landing.page',
                'login',
                'login.check',
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

            return response()->view('error.maintenance', [], 503);
        }

        return $next($request);
    }
}
