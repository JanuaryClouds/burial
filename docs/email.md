# Immediate email and password recovery

All application mail should go through `ImmediateMailService`. It persists a delivery, claims it atomically, sends SMTP during the HTTP request, and records the outcome. The browser uses `fetch` and shows a sending indicator. No queue, job, scheduler, cron, or post-response execution is used. SMTP acceptance is recorded as sent; downstream delivery and bounces are not tracked.

## Local Mailpit

Run Mailpit separately, then configure the local environment:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_SCHEME=smtp
MAIL_REQUIRE_TLS=false
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_TIMEOUT=15
MAIL_FROM_ADDRESS=burial@example.test
MAIL_FROM_NAME="Burial Assistance"
EMAIL_LOGS_MOCK=true
```

Mailpit's usual inbox is `http://127.0.0.1:8025`. Clear cached configuration after changing settings with `php artisan config:clear`, then run `php artisan email-logs:mock-init`. Sending requires a successful suppression check, including when using Mailpit. These examples do not change the actual environment or start Mailpit.

## Shared email standards

Every message passing through Laravel's mailer (Mailables, notifications, raw mail, and password recovery) receives the global `X-SES-MESSAGE-TAGS: site=burial` header. Existing valid non-site tags are preserved; conflicting site tags are replaced. Invalid non-site tags stop sending. Direct third-party SMTP clients outside Laravel do not pass through these events and must not be used for application mail.

Before SMTP, every To, Cc, and Bcc recipient is validated and normalized to lowercase with surrounding whitespace removed. The separate shared-log connection is queried for any Permanent Bounce or Complaint, regardless of site or event age. Any blocked address stops the whole message. No allowed result is cached, so manual retries see newly recorded suppressions. Send, Delivery, DeliveryDelay, Transient Bounce, Open, and Click do not suppress recipients or trigger another send.

Burial reads the externally maintained `email_logs` table through `email_logs_db`. Set the following independently of the application's `DB_*` settings, using a database account with SELECT-only access:

```dotenv
EMAIL_LOGS_MOCK=false
EMAIL_LOGS_DB_HOST=shared-log-host
EMAIL_LOGS_DB_PORT=3306
EMAIL_LOGS_DB_DATABASE=shared_email_logs
EMAIL_LOGS_DB_USERNAME=burial_email_reader
EMAIL_LOGS_DB_PASSWORD=replace-with-issued-secret
```

Connection attempts have a five-second timeout. Missing configuration, an unavailable database, or a missing table stops sending; the immediate mail service records `shared_logs_unavailable`, which a superadmin may retry after recovery. It never falls back to the main database. Production mock mode is rejected. No real shared tables are created, migrated, seeded, updated, or deleted by Burial.

Suppressed deliveries record `suppressed`, discard their sensitive payload, and disable manual retry. Correct the account's email and start a new recovery request. The public recovery acknowledgement stays generic. Raw database diagnostics and credentials are not exposed in these failure descriptions.

The existing local `sent` outcome means SMTP acceptance. Shared `Send` means SES acceptance, while `Delivery` means acceptance by the recipient's server, possibly into spam. `DeliveryDelay` means SES is still trying. Open and Click depend on tracking and do not prove a human read. This release does not correlate real SES IDs, receive webhooks, or display shared event history.

## Local mock event scenarios

With `APP_ENV=local` and `EMAIL_LOGS_MOCK=true`, the app checks a separate SQLite database at `storage/app/private/email-logs-mock/email-logs.sqlite`. The directory is ignored by Git. Use `email-logs:mock-init` explicitly; ordinary application migrations do not create this table. Re-running initialization preserves existing events and does not duplicate fixtures.

Fixture recipients are `delivered@example.test`, `permanent@example.test`, `complaint@example.test`, `mailboxfull@example.test`, `delayed@example.test`, and `other-site@example.test`. The other-site Permanent Bounce proves suppression is shared across systems. Delivered fixtures include repeated Open/Click events. Synthetic fixtures use `mock-fixture-*` message IDs, `mock-mailpit` caller identity, and `127.0.0.1` source metadata.

After Mailpit accepts a new message, the mock records one Send event per distinct recipient under the transport message ID. Other outcomes are only simulated explicitly. To append an event for an actual local test account (use its recipient address and mock message ID):

```powershell
php artisan email-logs:mock-event person@example.test Delivery mock-message-123
php artisan email-logs:mock-event person@example.test Bounce mock-message-123 --bounce-type=Permanent --bounce-subtype=NoEmail
php artisan email-logs:mock-event person@example.test Complaint mock-message-123 --site=another-system
php artisan email-logs:mock-event mailboxfull@example.test Bounce mock-message-456 --bounce-type=Transient --bounce-subtype=MailboxFull
```

Bounce requires a type (Permanent, Transient, or Undetermined) and subtype. Bounce fields are rejected for other events. Commands only write in local/testing mock mode and never connect to the production shared-log database. Mock IDs and events are not real SES outcomes. A mock log write failure after SMTP acceptance emits a safe warning and leaves delivery successful, preventing an unnecessary resend. Historical suppressions remain effective even if newer Delivery events exist.

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

Run `php artisan test --filter='SharedEmailStandardsTest|PasswordRecoveryTest|AccountManagementTest'` for global mail policy, mock commands, suppression failures, recovery, and account regressions. The shared-log tests use a separate in-memory SQLite connection; they never use the real shared-log database. Mail fakes bypass Laravel mail events, so policy tests use a real array transport.

`php artisan test --filter=PasswordRecoveryTest` uses isolated in-memory SQLite and an array transport or simulated transport failures. Run account-management regressions alongside it. To enable the optional real SMTP smoke test against local Mailpit, set `BURIAL_MAILPIT_SMOKE=1` in the test command's environment; it sends synthetic messages, forces a local connection refusal, and verifies manual retry through SMTP. It never changes the configured application database or environment file. SQLite tests validate claims and replay rejection; actual concurrent row-lock behavior relies on the production database supporting transactions and row locks.

For a local manual check: request recovery for an active internal account, inspect the link in Mailpit, reset and sign in; then stop Mailpit, request another reset, inspect Failed Emails as superadmin, restart Mailpit, and retry. Confirm unknown/citizen/inactive addresses show the same acknowledgement. SMTP credentials and real Hostinger connectivity still require a production smoke test once supplied.
