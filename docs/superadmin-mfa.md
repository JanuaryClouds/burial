# Superadmin authenticator security

All superadmins must use an authenticator app at every login. There is no remembered-device exemption or environment bypass. Ordinary staff and citizen authentication are unchanged.

## Deployment and enrollment

1. Install committed dependencies with `composer install`. The TOTP implementation uses `spomky-labs/otphp`; no Node.js or frontend build is required.
2. Back up the database and application encryption key using the normal deployment procedure, then apply `2026_11_19_120000_create_superadmin_mfa_credentials` before serving the new code. The migration adds credential storage and token MFA versions; it does not enroll accounts or reset passwords.
3. Refresh cached configuration using the normal deployment procedure. Existing superadmin sessions without MFA proof must sign in again. Existing superadmin API tokens without a matching MFA version are rejected.
4. After password verification, an unenrolled superadmin receives a locally generated QR code and a manual setup key. Scan it using a TOTP-compatible authenticator and confirm its current six-digit code. Save the recovery codes and acknowledge the instructions to finish signing in.

Pending login expires ten minutes after password verification. Required initial-password changes happen in this restricted flow before enrollment. Normal authentication, replacement of the previous active browser session, and fileserver token issuance happen only after MFA and recovery-code acknowledgment are complete. Failed or abandoned MFA does not evict a different browser's existing session.

Use HTTPS in production. TOTP uses SHA-1, six digits, and 30-second periods with one adjacent period on either side for clock tolerance. Keep server and phone clocks synchronized automatically; displayed time zones can differ. No network call to an authenticator vendor is needed. Development and production must use separate secrets.

## Protected changes

A fresh code is required for every system-settings save, disabling another internal account, and granting or removing another internal account's superadmin role. Viewing settings, citizen-account disabling, reactivation, and ordinary operational-role edits do not require an additional code. The initial login MFA requirement still applies to the acting superadmin.

Enter the code in the relevant settings or account form. Code consumption and the protected mutation share a database transaction, with credential row locking. Each accepted time step can be used only once across login and actions. A code just used for login cannot approve another change; wait for the next code. Five failed verification attempts per account in a minute trigger throttling across enrollment, login, recovery, and sensitive actions.

Superadmin promotion and demotion use explicit buttons, separate from operational roles. Only active internal accounts can be promoted. Changing the role revokes the target's sessions and API tokens. Newly promoted users must enroll at their next login if not already enrolled. Self-demotion, self-disabling, and removing the last active superadmin are prohibited. The existing ordered administrator lock set protects concurrent role/status updates.

## Recovery and factor changes

Enrollment issues ten random, 128-bit recovery codes. Plaintext is displayed once with copy and download controls; only SHA-256 hashes remain in the database. Store codes securely and separately from the authenticator. Each code is single-use, and replacing a recovery-code set invalidates every old code.

After password verification, choose **Lost your authenticator?** and enter a recovery code. Its acceptance immediately invalidates the old authenticator, all remaining recovery codes, sessions, and API tokens. Only replacement enrollment is permitted until a new authenticator is confirmed. New recovery codes are then issued. An interrupted replacement remains restricted; signing in again resumes enrollment. Recovery codes cannot approve settings, disabling, or role changes.

Owners can use **Authenticator security** on their own profile to replace an authenticator or generate new recovery codes. Both require the current password and a fresh authenticator code and revoke prior sessions/tokens. Superadmins cannot turn MFA off. Email password recovery changes the password but preserves MFA.

If both the authenticator and recovery codes are lost, an authorized server operator must first verify ownership outside the application, then run:

```powershell
php artisan superadmin:mfa-reset administrator@organization.example
```

The command requires an interactive audit reason identifying the operator and explicit confirmation. Do not enter passwords, codes, or secrets in the reason. It revokes the old factor, recovery set, sessions, and API tokens; the owner must verify their password and enroll again. It does not change the account's password, email, roles, or active status. There is no browser-accessible administrator reset or email-based MFA bypass.

## Operations and data protection

Secrets are encrypted using Laravel's application key, and credential material is hidden from serialization. Secure the database, environment file, backups, and `APP_KEY` independently. Do not regenerate or discard a deployed application key: existing encrypted credentials and other application data depend on it. Any key rotation must follow Laravel's supported previous-key and re-encryption process for the installed version and include a tested rollback. If credential data cannot be decrypted, fail closed and investigate; do not disable MFA.

Never log submitted MFA codes, recovery codes, setup keys, QR contents, or sensitive request bodies. MFA screens use no-store responses, and Debugbar collection is disabled on MFA routes, superadmin requests, and Livewire requests. The `superadmin-security` activity log records verification failures, enrollment, recovery, resets, role changes, and internal disabling without credentials. Security notifications use the existing immediate-mail delivery and retry tracking; mail failures do not undo security changes.

`SUPERADMIN_EMAIL_ADDRESS` and `SUPERADMIN_PASSWORD` remain initial seeding settings. Changing these values does not reset MFA or override existing credentials. Do not use demo seeders for production provisioning.

## Verification

Run the isolated SQLite MFA suite with `php artisan test tests/Feature/SuperadminMfaTest.php`. It covers restricted login/enrollment, expiry, recovery, replay, throttling, protected service and Livewire actions, token restrictions, password recovery, serialization, and console reset. Its MySQL concurrency test is skipped by default.

To verify row-lock behavior, set `$env:MFA_TEST_MYSQL='1'` in PowerShell and run `php artisan test tests/Feature/SuperadminMfaTest.php --filter=mysql_concurrent`. This creates a randomly named `burial_mfa_test_*` database using the testing MySQL connection, runs simultaneous TOTP use, recovery use, disabling, and demotion attempts, and drops only that database on completion. The configured database user needs create/drop privileges. Remove the temporary environment override afterwards with `Remove-Item Env:MFA_TEST_MYSQL`. No shared-database migrations or seeders are run.
