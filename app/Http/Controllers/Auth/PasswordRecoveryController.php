<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\PasswordPolicy;
use App\Services\Auth\PasswordRecoveryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class PasswordRecoveryController extends Controller
{
    public const MESSAGE = 'If an eligible account matches this email, a password reset link will be sent.';

    public function forgot()
    {
        return response()->view('auth.forgot-password')->header('Cache-Control', 'no-store')->header('Referrer-Policy', 'no-referrer');
    }

    public function form(Request $request, string $token)
    {
        return response()->view('auth.reset-password', ['token' => $token, 'email' => (string) $request->query('email', '')])
            ->header('Cache-Control', 'no-store')->header('Referrer-Policy', 'no-referrer');
    }

    public function send(Request $request, PasswordRecoveryService $service)
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:254']]);
        $ip = 'recovery.ip.'.hash('sha256', (string) $request->ip());
        $email = 'recovery.email.'.hash('sha256', mb_strtolower($data['email']));
        if (RateLimiter::attempt($ip, 5, fn () => true, 60)) {
            RateLimiter::attempt($email, 1, function () use ($service, $data) {
                try {
                    $service->request($data['email']);
                } catch (\Throwable) {
                    // Never let transport errors or encrypted payloads reach exception rendering/logging.
                    logger()->error('Password recovery could not complete. Check database and email configuration.');
                }
            }, 60);
        }

        return $request->expectsJson() ? response()->json(['message' => self::MESSAGE]) : back()->with('success', self::MESSAGE);
    }

    public function reset(Request $request, PasswordRecoveryService $service)
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:254'], 'token' => ['required', 'string', 'max:255'],
            'password' => [...PasswordPolicy::rules(), 'confirmed'], 'password_confirmation' => ['required', 'string']]);
        $service->reset($data);
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $message = 'Your password has been reset. Please sign in.';

        return $request->expectsJson()
            ? response()->json(['message' => $message, 'redirect' => route('admin.login')])
            : redirect()->route('admin.login')->with('success', $message);
    }
}
