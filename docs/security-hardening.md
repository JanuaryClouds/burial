# Security controls

UUID-only SSO is available only when `APP_ENV` is `local` or `testing` and
`PORTAL_USERS_ENABLE_GET=false`. Other environments return 403. Production must
enable signed Portal callbacks and configure the existing SSO secret. The Portal
payload and HMAC protocol are unchanged; nonce replay protection is not added.

`config/security.php` defines image limits (8,000 pixels per side and 25 million
pixels total) and authenticated user quotas (10 document generations and 120
document downloads per minute). Each quota is shared across its corresponding
routes. Rejected requests return 429 and `Retry-After`. Use a persistent cache
shared by all application workers; file cache works only when workers share the
same cache directory. These controls do not replace hosting/CDN DDoS protection.

Application uploads accept one image per applicable active CMS document, with
additional Muslim document fields when the beneficiary's religion is `Islam`.
Dimension checks happen before full decoding or remote uploads. Existing file-size
and mandatory-document behavior remain unchanged.

Livewire Toastr messages and titles use `escapeHtml`; the separate session
iziToast renderer escapes its message immediately before JSON serialization.
Messages remain plain text. CSP remains report-only by default.

## Verification

Run `php vendor/bin/phpunit tests/Feature/SecurityHardeningTest.php` for isolated
SQLite tests. No configured application database is used.

For the toast browser fixture, run:

```powershell
php -S 127.0.0.1:8773 tests/browser/toast-security-router.php
```

Open `http://127.0.0.1:8773` in a browser and verify `passed: true`. The fixture
uses the application's actual Toastr bundle and module, renders session toasts
through Blade, and loads iziToast 1.4.0 from cdnjs (network access required). It
uses array sessions/cache and an in-memory database. Stop the server afterward.

## Staff authentication monitoring

`spatie/laravel-suspicious-login` 1.0 provides local risk evaluation through
`AuthenticationAudit` and `AuthenticationMonitorPreset`. Automatic package auth
listeners are disabled and removed; explicit staff-only integration preserves
the existing session listener. Unknown-email and citizen authentication attempts
are excluded. Inactive internal accounts are included. Actor and affected-account
IDs are separate; a failed credential does not identify an authenticated actor.

The monitor records completed logins, failed password/MFA checks, cooldown
rejections, MFA pending/enrollment/recovery events, password recovery/change,
logout, rejected or invalidated sessions, staff role/status changes, invitation
audits, role-management audits, and actual request/action authorization denials.
It does not log ordinary Blade permission checks. New authentication/security
activities contain a numeric score, level, outcome, and readable reasons.
Historical security entries are classified without rewriting them and show
“Not assessed.” An unavailable assessment is distinct from a score of zero.

Defaults are new browser/device 25, new IP 10, five prior credential failures
within 15 minutes 30, suspicious user agent 20, and failures across ten staff
accounts on one IP within 60 minutes 40. Scores cap at 100; medium begins at 40,
high at 70. Package history contains only genuine credential attempts. Other
security actions are evaluated transiently and never establish trusted browsers
or inflate credential-failure history. Location, country, travel, and reputation
checks are disabled. Browser recognition uses an encrypted HTTP-only device
cookie; it is a browser identifier, not proof of identity or a physical device.

Scoring never blocks a login, replaces MFA, or changes per-email password
cooldowns. It never sends mail, calls Portal notifications, performs HTTP
lookups, or dispatches jobs. Existing MFA/recovery email behavior is unchanged.
Monitoring errors produce sanitized application diagnostics and do not introduce
new authentication restrictions. Authenticated activity records persist their
account references/names so later role changes or deletion do not erase history.

Medium/high events notify the affected owner through
`NotificationService::recordInternal()`. Repeated reasons for one recipient are
grouped for 15 minutes; medium-to-high escalation creates an immediate warning.
Every audit event is retained. Notifications contain no restricted audit URL.
Owners see only their notifications; only superadmins can inspect full logs.
The existing Activity Logs page loads General Activity above Authentication and
Authorization, independently through Livewire, and refreshes every 10 seconds.
Both table rows and the existing detail view display assessment information.

Run the isolated automated regression with
`php artisan test --compact tests/Feature/AuthenticationMonitoringTest.php`.
No browser, live notification endpoints, mail, or configured shared database is
used by this suite. Server-side tests do not establish browser/Hostinger behavior.
