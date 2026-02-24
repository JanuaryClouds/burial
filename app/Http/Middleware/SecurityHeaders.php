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

        if (config('app.env') === 'production') {
            $response->headers->set('X-Frame-Options', 'DENY');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('Referrer-Policy', 'strict-origin');
            $response->headers->set(
                'Permissions-Policy',
                'camera=(), microphone=(), geolocation=(), payment=(), usb=(), accelerometer=(), gyroscope=(), magnetometer=()'
            );

            $csp = "default-src 'self' "
                . "script-src 'self' 'nonce-$nonce' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.datatables.net; "
                . "style-src 'self' 'unsafe-inline' https://cdn.datatables.net https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; "
                . "connect-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.datatables.net; "
                . "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com; "
                . "img-src 'self' data:; " // TODO Update when fileserver API is working
                . "form-action 'self'; "
                . "object-src 'none'; "
                . "frame-ancestors 'none'; "
                . "base-uri 'self';";
    
            $response->headers->set('Content-Security-Policy', $csp);
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains;');
        }

        return $response;
    }
}
