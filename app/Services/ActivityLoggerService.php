<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Throwable;

class ActivityLoggerService
{
    /**
     * Log Exception Errors such as invalid forms
     * @param Throwable $exception
     * @param string $description
     * @return \Spatie\Activitylog\Contracts\Activity|null
     */
    public static function logException(Throwable $exception, string $description = 'Application Error'): Activity
    {
        return activity('system-errors')
            ->tap(function(Activity $activity) {
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
     * @return \Spatie\Activitylog\Contracts\Activity|null
     */
    public static function logUnauthorized() : Activity
    {
        return activity('permissions')
            ->tap(function(Activity $activity) {
                if (Auth::check()) {
                    $activity->causedBy(Auth::user());
                }
            })
            ->withProperties([
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Unauthorized access attempt');
    }

    /**
     * Log successful operations
     * @return \Spatie\Activitylog\Contracts\Activity|null
     */
    public static function logSuccess(string $description = 'Success', array $properties = []): Activity
    {
        return activity('success')
            ->tap(function(Activity $activity) {
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