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
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->bound('debugbar') && (! app()->environment('local', 'testing')
            || $request->routeIs('mfa.*', 'admin.login', 'login.check', 'password.*', 'invitation.*', 'sso', 'sso.logout', 'user.password')
            || $this->containsCredentials($request->all()))) {
            app('debugbar')->disable();
        }
        // Nonces belong to documents, never users or sessions.
        $nonce = base64_encode(random_bytes(32));
        $request->attributes->set('csp_nonce', $nonce);
        // Debugbar detects this binding when it injects its own trusted scripts.
        // This does not require Vite or rewrite arbitrary response HTML.
        app()->instance('csp-nonce', $nonce);
        view()->share('nonce', $nonce);

        $response = $next($request);

        if (! str_contains(strtolower($response->headers->get('Content-Type', '')), 'text/html')) {
            return $response;
        }
        $mode = config('csp.mode', 'report-only');
        if ($mode === 'off') {
            return $response;
        }
        $directives = config('csp.directives', []);
        $directives['script-src'][] = "'nonce-{$nonce}'";
        $policy = collect($directives)->map(fn ($sources, $directive) => $directive.' '.implode(' ', $sources))->implode('; ');
        $header = $mode === 'enforce' ? 'Content-Security-Policy' : 'Content-Security-Policy-Report-Only';
        $response->headers->set($header, $policy.';');
        // Shared HTML caches must not reuse a document nonce for another visitor.
        $response->headers->set('Cache-Control', 'private, no-store');

        return $response;
    }

    /**
     * Livewire sends component state as JSON snapshots, alongside nested updates.
     *
     * @param  array<array-key, mixed>  $values
     */
    private function containsCredentials(array $values): bool
    {
        foreach ($values as $key => $value) {
            $name = strtolower(preg_replace('/[^a-z]/i', '', (string) $key));
            foreach (['password', 'mfacode', 'secret', 'recoverycode', 'claimcode', 'customvalues'] as $credential) {
                if (str_contains($name, $credential)) {
                    return true;
                }
            }
            if (is_array($value) && $this->containsCredentials($value)) {
                return true;
            }
            if ($name === 'snapshot' && is_string($value)) {
                $snapshot = json_decode($value, true);
                if (! is_array($snapshot) || $this->containsCredentials($snapshot)) {
                    return true;
                }
            }
        }

        return false;
    }
}
