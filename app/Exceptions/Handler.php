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
            // return response()->view('errors.main', [], 403);
            return redirect()->to(url()->previous() ?? route('landing.page'))
                ->with('error', 'You do not have permission to access this page.');
        } elseif ($exception instanceof NotFoundHttpException) {
            return redirect()->to(url()->previous() ?? route('landing.page'))
                ->with('error', 'You do not have permission to access this page.');
            // return response()->view('errors.main', [], 404);
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
