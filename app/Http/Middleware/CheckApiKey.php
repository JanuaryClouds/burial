<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $providedApiKey = $request->header('X-API-KEY');
        $validApiKeys = config('services.api_keys');
        if (!$providedApiKey || !in_array($providedApiKey, $validApiKeys, true)) {
            return response()->json([
                'message' => 'Invalid or missing API key.',
            ], 401);
        }
        
        return $next($request);
    }
}
