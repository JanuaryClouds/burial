# Immediate email and password recovery

All application mail should go through `ImmediateMailService`. It persists a delivery, claims it atomically, sends SMTP during the HTTP request, and records the outcome. The browser uses `fetch` and shows a sending indicator. No queue, job, scheduler, cron, or post-response execution is used. SMTP acceptance is recorded as sent; downstream delivery and bounces are not tracked.

## Local Mailpit

Run Mailpit separately, then configure the local environment:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_SCHEME=smtp
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_TIMEOUT=15
MAIL_FROM_ADDRESS=burial@example.test
MAIL_FROM_NAME="Burial Assistance"
```

Mailpit's usual inbox is `http://127.0.0.1:8025`. Clear cached configuration after changing settings with `php artisan config:clear`. These examples do not change the actual environment or start Mailpit.

## Hostinger SMTP

Obtain the team's SMTP host, port, username, password, allowed From address, and TLS requirements before enabling production mail. Set them in the server environment, never source control or the settings frontend. Use `MAIL_MAILER=smtp`, `MAIL_SCHEME=smtps` for implicit TLS (usually port 465), or `MAIL_SCHEME=smtp` for STARTTLS (usually port 587). SMTP automatically negotiates STARTTLS when advertised. Require TLS for production with `MAIL_REQUIRE_TLS=true`; keep certificate verification enabled. `MAIL_URL`, if present, overrides individual SMTP settings and must be reviewed or removed. The old `MAIL_ENCRYPTION` setting is replaced by `MAIL_SCHEME` for the installed Laravel/Symfony transport.

Set `APP_URL` to the public HTTPS origin, including any application subdirectory. Recovery URLs are built from this value, not the request Host header. The SMTP timeout defaults to 15 seconds; the web server/PHP request timeout must allow for database operations, rendering, and SMTP. Production delivery rejects non-SMTP mailers, including log fallback. Cache configuration again after deployment using `php artisan config:cache`.

Before deploying, review and apply `2026_11_03_100000_create_mail_deliveries.php` through the normal audited migration process. It creates `mail_deliveries` and `mail_delivery_attempts` without altering existing account data. No seeders are required. Back up the database and preserve `APP_KEY`: recipient and payload encryption depends on it. This implementation does not apply migrations to configured/shared databases.

## Recovery and manual retry

Active internal accounts, including superadmins, can request recovery from the internal login screen. Citizens recover through the Portal. Requests always show a generic acknowledgement; transport failures are visible only to active superadmins under **System → Failed Emails**. There is no additional sidebar link.

Reset tokens expire after 60 minutes and are consumed once. Requests are limited to one per email per minute and five per IP per minute. Reset submissions and administrator retries have separate five-per-minute limits. Resetting clears the initial-password flag, revokes API tokens, and invalidates existing staff sessions through the password-hash comparison. It does not automatically sign the user in.

A failed recovery email reuses a valid token on retry. An expired or replaced token is regenerated. Account deactivation, loss of internal access, email changes, or a password change blocks that recovery message. Successful messages and permanently ineligible messages have their sensitive payload removed; attempts retain safe outcomes only. Raw SMTP exceptions, credentials, tokens, and bodies are not included in failure descriptions.

An interrupted send remains `sending` until its claim is at least five minutes old (or ten times the configured SMTP timeout, whichever is greater). It then appears as uncertain in Failed Emails; no timer or background process is necessary. A record left pending before its first claim also appears after that grace period. A manual retry records an interrupted sending attempt as uncertain before claiming a new attempt. The UI warns that it may send a duplicate: exactly-once SMTP delivery cannot be guaranteed if a connection or PHP request dies after acceptance. Do not retry while an original server request is still running. Keep host request lifetimes below the interruption grace period.

## Extending mail

Add an immutable payload implementing `MessagePayload`, a handler with `prepare(MailDelivery)` returning a non-queued Mailable, and register its type in `ImmediateMailService`. Persist through `record()` inside a business transaction if necessary; call `deliver()` only after commit. Alternatively, `send()` records and delivers directly outside transactions. Handlers must recheck current authorization and whether retrying still makes sense. Never serialize arbitrary PHP objects, Mailable instances, credentials, or untrusted template names into delivery payloads.

## Verification

`php artisan test --filter=PasswordRecoveryTest` uses isolated in-memory SQLite and an array transport or simulated transport failures. Run account-management regressions alongside it. To enable the optional real SMTP smoke test against local Mailpit, set `BURIAL_MAILPIT_SMOKE=1` in the test command's environment; it sends synthetic messages, forces a local connection refusal, and verifies manual retry through SMTP. It never changes the configured application database or environment file. SQLite tests validate claims and replay rejection; actual concurrent row-lock behavior relies on the production database supporting transactions and row locks.

For a local manual check: request recovery for an active internal account, inspect the link in Mailpit, reset and sign in; then stop Mailpit, request another reset, inspect Failed Emails as superadmin, restart Mailpit, and retry. Confirm unknown/citizen/inactive addresses show the same acknowledgement. SMTP credentials and real Hostinger connectivity still require a production smoke test once supplied.
