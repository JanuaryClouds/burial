<?php

namespace App\Services\Auth;

use Spatie\SuspiciousLogin\Checks\CredentialStuffingCheck;
use Spatie\SuspiciousLogin\Checks\FailedAttemptsCheck;
use Spatie\SuspiciousLogin\Checks\NewDeviceCheck;
use Spatie\SuspiciousLogin\Checks\NewIpCheck;
use Spatie\SuspiciousLogin\Checks\SuspiciousUserAgentCheck;
use Spatie\SuspiciousLogin\Presets\MonitorOnlyPreset;

class AuthenticationMonitorPreset extends MonitorOnlyPreset
{
    public function checks(): array
    {
        return [
            NewDeviceCheck::new()->score((int) config('suspicious-login.checks.new_device')),
            NewIpCheck::new()->score((int) config('suspicious-login.checks.new_ip')),
            FailedAttemptsCheck::new()->score((int) config('suspicious-login.checks.failed_attempts.score'))
                ->threshold((int) config('suspicious-login.checks.failed_attempts.threshold'))
                ->withinMinutes((int) config('suspicious-login.checks.failed_attempts.minutes')),
            SuspiciousUserAgentCheck::new()->score((int) config('suspicious-login.checks.suspicious_user_agent')),
            CredentialStuffingCheck::new()->score((int) config('suspicious-login.checks.credential_stuffing.score'))
                ->threshold((int) config('suspicious-login.checks.credential_stuffing.threshold'))
                ->withinMinutes((int) config('suspicious-login.checks.credential_stuffing.minutes')),
        ];
    }
}
