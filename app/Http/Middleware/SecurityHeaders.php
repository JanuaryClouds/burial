<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = base64_encode(random_bytes(16));
        view()->share('nonce', $nonce);
        
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin');

        if (config('app.env') === 'production') {
            $csp = "default-src 'self' 'unsafe-inline'; "
                . "script-src 'self' 'nonce-$nonce' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.datatables.net; "
                . "style-src 'self' 'unsafe-inline' https://cdn.datatables.net https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; "
                . "connect-src 'self' 'nonce-$nonce' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.datatables.net; "
                . "font-src 'self' data: https://cdnjs.cloudflare.com; "
                . "img-src 'self' data:; " // TODO Update when fileserver API is working
                . "object-src 'none'; "
                . "frame-ancestors 'none'; "
                . "base-uri 'self';";
    
            $response->headers->set('Content-Security-Policy', $csp);
        }

        return $response;
    }
}
