# TLC Citizens Portal

## Context

The TLC Portal is maintained by a separate city LGU team. It owns citizen credentials and provides a dashboard linking citizens to LGU services, including this Funeral Assistance System. This system uses Portal SSO and its user API rather than a separate citizen password-login flow.

## Required behavior

1. Verify the SSO payload signature according to the Portal contract before trusting its contents. Decode the payload and use its citizen UUID to retrieve Portal account data. Create a citizen account only when no existing account or identity conflict exists; otherwise use the matching citizen account.
2. The Portal citizen UUID and email are immutable, both in the Portal and in this system. An existing account must match both. Do not change either value during synchronization, merge accounts, or relink an account to make a mismatched identity pass authentication. An identity mismatch or collision must prevent login.
3. Citizens have a `citizen_uuid` and no Spatie roles. They must enter through SSO. Do not authenticate them through `/admin/login`; direct them to the configured Portal link.
4. Internal accounts have assigned roles and must enter through `/admin/login`. Do not authenticate them through citizen SSO or permit an account to have both `citizen_uuid` and assigned roles.
5. On every successful SSO login, synchronize first name, middle name, last name, suffix, and contact number. Only middle name and suffix may be empty. Explicitly empty middle name or suffix clears the local value. Email and UUID remain unchanged.
6. If the Portal user API is unavailable or returns incomplete required account data, do not complete citizen authentication; show the system's error page. Portal failure must not prevent internal login.

## Exceptions and constraints

- The Portal database primary key is a separate incrementing ID. This application uses the Portal's secondary UUID as `citizen_uuid`, not that primary key.
- The Portal team has specified that entry links must remain usable without payload expiration because citizens may leave their dashboard open. Preserve signature validation; changes to expiry require coordination with that team.
- Local/testing entry without the remote Portal is an existing development facility. It must remain unavailable outside local/testing and does not establish a production fallback.

## Implementation references and known differences

- [SSO controller](../../app/Http/Controllers/CitizenAccessController.php), [Portal client](../../app/Services/CentralClientService.php), [account classification](../../app/Services/Auth/AccountAccess.php), and [configuration](../../config/services.php).
- [Deployment environment](../../docs/deployment-environment.md) describes configuration names and capabilities.
- Portal synchronization rejects incomplete profiles, UUID mismatches, and immutable-email mismatches before establishing the SSO session used for walk-in claiming. Fixture-email shortcuts are restricted to local/testing mock mode. Error responses still differ by failure path.
