<?php

namespace App\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Contracts\Activity as ActivityContract;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ActivityLoggerService
{
    /**
     * Log Exception Errors such as invalid forms
     */
    public static function logException(Throwable $exception, string $description = 'Application Error'): ?ActivityContract
    {
        if ($exception instanceof AuthorizationException
            || $exception instanceof UnauthorizedException
            || ($exception instanceof HttpExceptionInterface && $exception->getStatusCode() === 403)) {
            return self::logUnauthorized();
        }

        return activity('system-errors')
            ->tap(function (Activity $activity) {
                if (Auth::check()) {
                    $activity->causedBy(Auth::user());
                }
            })
            ->withProperties([
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
                'request' => [
                    'url' => request()->fullUrl(),
                    'method' => request()->method(),
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ],
            ])
            ->log($description);
    }

    /**
     * Log unauthorized access and requests
     */
    public static function logUnauthorized(): ?ActivityContract
    {
        return activity('permissions')
            ->tap(function (Activity $activity) {
                if (Auth::check()) {
                    $activity->causedBy(Auth::user());
                }
            })
            ->withProperties([
                'route' => request()->route()?->getName(),
                'method' => request()->method(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Unauthorized access attempt');
    }

    /**
     * Log successful operations
     */
    public static function logSuccess(string $description = 'Success', array $properties = []): ?ActivityContract
    {
        return activity('success')
            ->tap(function (Activity $activity) {
                if (Auth::check()) {
                    $activity->causedBy(Auth::user());
                }
            })
            ->withProperties([
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'details' => $properties,
            ])
            ->log($description);
    }
}
