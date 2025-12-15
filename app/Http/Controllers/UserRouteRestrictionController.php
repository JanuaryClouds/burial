<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRouteRestriction;
use Illuminate\Http\Request;
use Route;
use Str;

class UserRouteRestrictionController extends Controller
{
    private function routes()
    {
        $routes = collect(Route::getRoutes())
            ->filter(fn ($route) => in_array('GET', $route->methods()) &&
                (Str::startsWith($route->uri, ['admin', 'reports'])) &&
                (! Str::contains($route->uri, 'dashboard'))
            )
            ->map(function ($route) {
                return [
                    'name' => $route->getName(),
                ];
            })
            ->filter(fn ($r) => ! empty($r['name']))
            ->values()
            ->pluck('name')
            ->toArray();

        return $routes;
    }

    public function manage($userId)
    {
        $user = User::findOrFail($userId);
        $routes = $this->routes();

        $restrictions = $user->routeRestrictions()->pluck('name')->toArray();

        return view('superadmin.user', compact(['routes', 'restrictions', 'user']));
    }

    public function update(Request $request, $userId)
    {
        $allowedRoutes = $request->input('allowed', []);
        $routes = $this->routes();
        $restrictedRoutes = array_diff($routes, $allowedRoutes);

        $user = User::findOrFail($userId);
        $user->routeRestrictions()->delete();

        $insertData = collect($restrictedRoutes)->map(fn ($route) => [
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'name' => $route,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        UserRouteRestriction::insert($insertData);

        return back()->with('alertSuccess', 'Page Access Updated for '.$user->first_name.' '.$user->last_name);
    }
}
