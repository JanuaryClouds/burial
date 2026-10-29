# Account management

Portal citizens have a citizen UUID and no internal roles. CMS/staff/superadmin
accounts have internal roles and no citizen UUID. Email collisions are rejected,
never merged. Staff sign in at `/admin/login`; citizens use the existing signed
portal callback. Its encoding, shared-secret HMAC, and nonce contract has no new
expiration or replay requirement. Portal status synchronization is not implemented.
Burial's `is_active` remains authoritative for local restrictions.

Inactive citizens can sign in and read records already authorized to them. They
cannot create, edit, cancel, upload, update their profile, or mark notifications
as read. Inactive staff and superadmins have no authenticated access. Deactivation
revokes personal access tokens; reactivation does not resurrect them. A superadmin
cannot disable themselves or the last active superadmin. CMS status changes lock
the administrator rows in ID order before checking this invariant.

Only an active internal account owner can change their password. A superadmin
may set an initial password during creation but cannot change somebody else's
password afterward. New internal accounts must change the initial password before
other access. Existing hashes and citizen UAT credentials are preserved.

Service-backed seeders and their factories use an explicit console-only
`SeederAccountContext` for the selected active internal actor. It suspends only
the initial-password gate for that call; roles, permissions, account status,
ownership, and workflow invariants remain enforced. The scope is restored even
on failure and never changes password hashes or the first-login flag. Browser
middleware and Livewire component restrictions still require the owner to change
the initial password.

New internal passwords require at least 12 characters, uppercase, lowercase,
a number, and a symbol. Spaces are preserved. The bcrypt limit is 72 bytes;
longer values are rejected before hashing. Password changes require the current
password, matching confirmation, and a different new password. Attempts are
limited to five per minute per account. No email reset or periodic expiration
flow is provided. Changes revoke tokens and require sign-in again. Staff sessions
retain a password-hash baseline checked on subsequent requests, including Livewire.

## Deployment

1. Back up the database and pause writers in an approved maintenance window.
2. Run `php artisan accounts:audit` read-only and review conflicting identities.
   Do not automatically clear UUIDs, assign roles, or change credentials.
3. Apply the new migration only after approval. It adds
   `users.password_change_required`, default false; no existing account is reset.
4. Deploy code together with the migration. Clear compiled Blade views and refresh
   the normal production configuration cache. Existing staff sessions must sign in
   again; citizen sessions retain their normal lifetime.
5. New internal seeder accounts require a compliant `ADMIN_PASSWORD`. Existing
   seeded account passwords and their first-login flags are not overwritten.
6. Verify initial password change, owner-only password change, inactive citizen
   reads and denied writes, inactive staff/token denial, and administrator guards.
   Confirm the portal's existing signed callback still works without an expiry.

The shared database migration, seeders, and account reconciliation must not run
without explicit approval. Automated account tests use isolated SQLite databases
and block outbound HTTP; MySQL row-lock concurrency requires separate validation.
