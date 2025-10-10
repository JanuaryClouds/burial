<?php

namespace App\Http\Middleware;

use App\Models\UserRouteRestriction;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RouteAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $route = $request->route()->getName();
        $restrictedRoutes = UserRouteRestriction::select('user_id', 'name')->pluck('name')->toArray();
        if (in_array($route, $restrictedRoutes)) {
            return back()->with('alertError', 'You do not have access to this page');
        }

        return $next($request);
    }
}
