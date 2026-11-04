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
