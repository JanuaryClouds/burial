<?php

use App\Services\Auth\AuthenticationMonitorPreset;
use Spatie\SuspiciousLogin\Fingerprinters\CookieFingerprinter;
use Spatie\SuspiciousLogin\Models\LoginAttempt;

return [
    'enabled' => false,
    'monitoring_enabled' => env('AUTHENTICATION_MONITORING_ENABLED', true),
    'preset' => AuthenticationMonitorPreset::class,
    'risk_thresholds' => ['medium' => 40, 'high' => 70],
    'geolocation' => ['driver' => 'null', 'cache_ttl' => 604800],
    'ip_reputation' => ['driver' => 'null', 'cache_ttl' => 86400],
    'device_fingerprinter' => CookieFingerprinter::class,
    'model' => LoginAttempt::class,
    'prune_after_days' => 365,
    'notification_window_seconds' => 900,
    'checks' => [
        'new_device' => 25,
        'new_ip' => 10,
        'failed_attempts' => ['score' => 30, 'threshold' => 5, 'minutes' => 15],
        'suspicious_user_agent' => 20,
        'credential_stuffing' => ['score' => 40, 'threshold' => 10, 'minutes' => 60],
    ],
];
