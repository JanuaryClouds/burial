<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof UnauthorizedException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You do not have permission to access this page.',
                ], 403);
            }

            return redirect()->back()
                ->with('error', 'You do not have permission to access this page.');
        } elseif ($exception instanceof NotFoundHttpException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The page you requested could not be found.',
                ], 404);
            }

            if (auth()->check() && auth()->user()->roles()->count() == 0) {
                return redirect()->route('landing.page')
                    ->with('error', 'The page you requested could not be found.');
            }

            return redirect()->route(auth()->check() ? 'dashboard' : 'landing.page')
                ->with('error', 'The page you requested could not be found.');
        } elseif ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You are not logged in.',
                ], 401);
            }

            return redirect()->route('landing.page')
                ->with('info', 'You are not logged in.');
        } elseif ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You do not have permission to access this page.',
                ], 403);
            }

            return redirect()->back()
                ->with('error', 'You do not have permission to access this page.');
        }

        return parent::render($request, $exception);
    }

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
