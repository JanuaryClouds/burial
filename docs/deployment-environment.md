# Maintainer environment and deployment guide

This guide accompanies the sectioned [`.env.example`](../.env.example). It explains what a deployment needs and records the differences between that template and the current implementation. Reviewed against the repository on November 11, 2026.

**Configuration status:** `config/services.php` reads the new integration names, with legacy names as fallbacks. Explicit new values (including blank endpoints and `false` flags) take precedence. Existing internal config paths remain compatible with current clients. Fileserver method-specific endpoints and API keys are exposed as `services.fileserver.get` and `.post`, but the upload/fetch client still uses the legacy URL/authentication contract described below.

## How to read the template

- **Prerequisites** are resources or agreements to obtain from the external system's maintainer before enabling an integration.
- **Required** means required for the feature or deployment described by that section. It does not mean every integration must be enabled to start the application.
- **Optional** means conditional or defaulted. A setting listed as optional can become mandatory when its feature is used; image encryption is one such case.
- GET and POST represent independently configured capabilities, not a choice between two operating modes. Use literal `true` or `false` for switches. A configured switch only controls behavior where a client actually checks it.

The intended convention is `<SERVICE>_<METHOD>_ENABLE`, `_ENDPOINT`, and `_API_KEY`. If the provider supplies one endpoint and key for both methods, repeat the issued values in both method groups. If it supports only one method, enable and configure that capability only. Separate variables do not establish that the application supports both capabilities or that the provider accepts both HTTP methods at the same URL.

Keep real credentials in the deployment environment, outside version control. Quote values containing spaces or `#`. A blank assignment is an explicit empty value; it does not necessarily select a configuration default. Remove unused optional assignments when you want their defaults. Keep each variable defined once.

## Application, database, and accounts

| Settings | Requirement and purpose |
| --- | --- |
| `APP_NAME`, `APP_URL` | Set the application label and canonical URL. Use the public HTTPS URL for UAT/production, including any application subdirectory. Recovery links use this URL. |
| `APP_ENV`, `APP_DEBUG` | Use `local` for development, an agreed value such as `uat` for UAT, and `production` for production. Use `APP_DEBUG=false` on shared deployments. The comment at the top of the example is only a human label. |
| `APP_KEY` | Required application encryption secret. Generate for a new installation with `php artisan key:generate`; preserve it on subsequent deployments and restores. Existing encrypted mail payloads depend on it. |
| `APP_PRODUCTION_URLS` | Optional comma-separated deployment URLs, exposed as `app.production_urls`; no current behavior or redirect policy consumes this metadata. |
| `SCOUT_DRIVER` | Search driver; the example selects `database`. |
| `DEBUGBAR_ENABLED` | Optional Debugbar switch; the example disables it. |
| `APP_TIMEZONE` | Optional, currently defaults to `UTC`; set explicitly if the deployment needs a different application timezone. |
| `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Application database connection. Use the intended MySQL database and issued credentials; the example's root account and database name are local placeholders. |
| `ADMIN_PASSWORD` | Needed when account seeders create internal accounts, subject to the application's password policy. It is an initial seeding secret, not a runtime override of existing passwords. Do not run demo seeders as production provisioning. |
| `CSWDO_DEPT_HEAD`, `CSWDO_SOCIAL_WELFARE_OFFICER`, `CSWDO_DEPARTMENT_EMAIL` | Supply the department's approved values. The system-setting seeder also reads these values into stored settings; changing the environment alone does not rewrite database records. |
| `CSWDO_WORK_HOURS_START`, `CSWDO_WORK_HOURS_END` | Department working hours. Config defaults are 7 and 16; the example explicitly selects 8 and 16. |
| `CSP_MODE` | Set explicitly to `report-only`, `enforce`, or `off`. Default when absent is `report-only`. Validate application and external-service compatibility before enforcement. |

`API_KEYS` is a comma-separated allowlist consumed by the application's API-key middleware. Issue secrets only for clients that need those protected routes. No application/configuration consumers were found for `API_TEST_KEY` or `API_FUNERAL_ASSISTANCE_KEY`; their presence in the example currently grants no access and they are not established runtime requirements.

## Integration name transition

Use the updated example names. The legacy column lists supported fallbacks for existing deployments, except where noted. Keep legacy fileserver URL/path settings until its client update ships. Omitted capability groups are not configurable operations: Portal users support GET and notifications support POST.

| Updated example | Legacy fallback | Notes |
| --- | --- | --- |
| `PORTAL_USERS_SSO_SECRET_KEY` | `SSO_SECRET_KEY` | Shared signing secret agreed with the Portal. |
| `PORTAL_USERS_GET_ENABLE` | `PORTAL_USERS_ENABLE_GET` | Citizen lookup and signed-entry paths use this flag. |
| `PORTAL_USERS_GET_ENDPOINT` | `PORTAL_USERS_ENDPOINT` | Citizen lookup URL. |
| `PORTAL_USERS_GET_API_KEY` | `PORTAL_USERS_API_KEY` | Citizen lookup credential. |
| `PORTAL_NOTIFICATION_POST_ENABLE` | `PORTAL_NOTIFICATION_ENABLE_POST` | Controls sending Portal notifications. |
| `PORTAL_NOTIFICATION_POST_ENDPOINT` | `PORTAL_NOTIFICATION_ENDPOINT` | Notification URL. |
| `PORTAL_NOTIFICATION_POST_API_KEY` | `PORTAL_NOTIFICATION_API_KEY` | Notification credential. |
| `PORTAL_USERS_SEEDER` | None | Controls preset citizen seeding. Explicit `false` skips presets. When absent, the legacy preset default remains true. Legacy `PORTAL_USERS_SEED_FROM_PORTAL=true` still selects remote seeding instead. |
| `FILESERVER_GET_ENABLE` | `FILESERVER_ENABLE_GET` | Controls image retrieval through `get()`. |
| `FILESERVER_POST_ENABLE` | `FILESERVER_ENABLE_POST` | Controls uploads. |
| `FILESERVER_GET_ENDPOINT`, `FILESERVER_POST_ENDPOINT` | `FILESERVER_ENDPOINT` plus `FILESERVER_API` | No direct split-endpoint support yet; see the fileserver details below. |
| `FILESERVER_GET_API_KEY`, `FILESERVER_POST_API_KEY` | No equivalent | Current remote requests do not use static method-specific API keys. |
| `SMS_POST_ENABLE` | `SMS_ENABLE_POST` | Controls sending SMS. |
| `SMS_POST_ENDPOINT` | `SMS_ENDPOINT` | SMS submission URL. |
| `SMS_POST_API_KEY` | `SMS_API_KEY` | SMS credential. |
| `PSA_CLASSIFICATION_GET_ENABLE` | `PSA_CLASSIFICATION` | Controls whether the PSA client can be constructed. |
| `PSA_CLASSIFICATION_GET_ENDPOINT` | `PSA_CLASSIFICATION_ENDPOINT` | Geographic lookup base URL. |
| `PSA_CLASSIFICATION_GET_VERSION` | `PSA_CLASSIFICATION_VERSION` | Version segment appended to the base URL. |
| `PSA_CLASSIFICATION_GET_API_KEY` | `PSA_CLASSIFICATION_API_KEY` | Lookup token. |

`PORTAL_ENDPOINT`, `PORTAL_MOCK`, and all `FILESERVER_MOCK*` variables retain their names. The configuration also exposes legacy `DISBURSEMENT_SYSTEM_ENABLE_GET`, `DISBURSEMENT_SYSTEM_ENABLE_POST`, `DISBURSEMENT_SYSTEM_ENDPOINT`, `DISBURSEMENT_SYSTEM_API_KEY`, which are absent from the example. Configuration entries alone do not establish an implemented integration.

## TLC Portal and notifications

Obtain the Portal return/logout URL, citizen lookup endpoint and API key, agreed SSO signing secret, and notification endpoint and key from the Portal team. Configure citizen GET and notification POST independently using the new names above. `PORTAL_ENDPOINT` is the return destination, not the citizen API endpoint.

`PORTAL_MOCK=true` exposes development citizen entry behavior; keep it false on shared deployments. Unsigned UUID entry is restricted to local/testing environments. Use `PORTAL_USERS_SEEDER=true` with mock mode for preset local seed data; `false` skips presets. Legacy `PORTAL_USERS_SEED_FROM_PORTAL` defaults to false. Setting it true makes the seeder request Portal users and requires a working citizen lookup integration; it does not require mock mode.

## Fileserver

Obtain the storage host, upload route, authentication contract, and byte-preservation requirements from the fileserver owner. The split endpoint/key design is represented in config, but still needs client implementation and real-service verification.

Current behavior in `ImageService` is:

- `FILESERVER_ENDPOINT` is the base URL; GET requests append `/burial/{filename}`.
- `FILESERVER_API` is an upload **path**, appended to that base URL. It is not an API key.
- Remote GET requests carry no API key. Remote uploads submit a token derived from the authenticated user's stored personal access token plus the current date, preserving the existing protocol.
- Uploads issue a GET existence check before POST, even with `FILESERVER_ENABLE_GET=false`. A provider with POST-only access will therefore need a client change before uploads can work.
- Disabled retrieval returns missing-image behavior; disabled uploads validate the input, then skip storage and log the skip. They do not create local document storage.

`FILESERVER_ENCRYPTION_KEY` is **required for encrypted image uploads and reads**, despite its placement under Optional in the example. Use a base64-encoded 32-byte key, optionally prefixed with `base64:`. This is separate from `APP_KEY`. Preserve the existing key for stored documents; replacing it does not re-encrypt old files.

For isolated local storage, see [Local fileserver](fileserver-mock.md). `FILESERVER_MOCK=true` is allowed only in local/testing. Supply a nonempty `FILESERVER_MOCK_TOKEN`; defaults are port `8099`, duplicate policy `reject` (`overwrite` is also supported), and maximum `32768` KiB (accepted range 1–32768). Mock mode substitutes its own endpoint and upload token. Enable the current GET/POST flags and provide the image encryption key as well.

## SMS and geographic classifications

SMS needs the provider's POST endpoint and issued key only when sending is enabled. There is no implemented SMS GET capability.

PSA needs the base endpoint, version, and token for address lookups. The service appends `/{version}/{geographic-level}` and sends the token as a query parameter. Its constructor now checks the GET enable value and refuses use when disabled. Disabling it does not provide offline address data.

## SMTP and shared email logs

For recovery email delivery, obtain an SMTP host, port, username/password, approved sender address, and TLS requirements. Configure `MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, and `MAIL_FROM_NAME`. Set `MAIL_SCHEME=smtps` for implicit TLS or `smtp` for STARTTLS according to the provider. Use `MAIL_REQUIRE_TLS=true` for production; the example explicitly sets false for local Mailpit. `MAIL_TIMEOUT` defaults to 15 seconds.

`MAIL_ENCRYPTION` is not read by the current SMTP configuration. Optional `MAIL_URL`, if supplied outside the example, overrides individual SMTP settings; review it when troubleshooting. `MAIL_EHLO_DOMAIN` is an optional SMTP identity.

Sending also requires a successful shared suppression check. With `EMAIL_LOGS_MOCK=false`, obtain the independent `EMAIL_LOGS_DB_HOST`, `EMAIL_LOGS_DB_PORT` (default 3306), `EMAIL_LOGS_DB_DATABASE`, `EMAIL_LOGS_DB_USERNAME`, and `EMAIL_LOGS_DB_PASSWORD`. Use SELECT-only access to the externally maintained `email_logs` table. Unavailable or incomplete shared-log configuration stops sending; it does not fall back to the application database.

For local/testing only, use `EMAIL_LOGS_MOCK=true` and explicitly run `php artisan email-logs:mock-init` to initialize isolated SQLite logs. Production mock mode is rejected. See [Email setup and operations](email.md) for Mailpit, suppression rules, migrations, and recovery/manual retry checks.

Sender identity belongs to SMTP configuration; shared-log credentials are independent.

## Infrastructure and optional drivers

| Group | Deployment requirement |
| --- | --- |
| `LOG_CHANNEL`, `LOG_DEPRECATIONS_CHANNEL`, `LOG_LEVEL` | Select configured channels and the desired logging level. The example uses `stack`, `null`, and `debug`; choose the operational log level for the deployment. |
| `CACHE_DRIVER`, `SESSION_DRIVER`, `SESSION_LIFETIME` | The example uses file-backed cache/sessions and a 120-minute session lifetime. Ensure storage is writable. Optional `SESSION_SECURE_COOKIE=true` is appropriate for an HTTPS-only deployment; set `SESSION_DOMAIN` only if needed. |
| `QUEUE_CONNECTION`, `BROADCAST_DRIVER`, `FILESYSTEM_DISK` | The example selects `sync`, `log`, and `local`. Immediate SMTP runs during the request without a queue worker. Local filesystem selection does not replace the external image service. |
| `MEMCACHED_HOST` | Only needed when selecting Memcached. |
| `REDIS_HOST`, `REDIS_PASSWORD`, `REDIS_PORT` | Only needed when a selected cache, session, or queue connection uses Redis. |
| `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION` | Conditional on an AWS-backed service. With ordinary SMTP and local disks, no AWS credentials are required merely because these placeholders exist. |
| `AWS_BUCKET`, `AWS_USE_PATH_STYLE_ENDPOINT` | Conditional on S3 storage. Optional `AWS_ENDPOINT` and `AWS_URL` are also supported by filesystem configuration. |
| `PUSHER_APP_ID`, `PUSHER_APP_KEY`, `PUSHER_APP_SECRET`, `PUSHER_HOST`, `PUSHER_PORT`, `PUSHER_SCHEME`, `PUSHER_APP_CLUSTER` | Conditional on choosing Pusher broadcasting instead of the example's log driver. |
| `VITE_*` | Retained template/build inputs. The supported Hostinger workflow uses existing public assets and has no Node/npm frontend build step. Do not put private secrets in frontend build variables. |

Other framework/package driver-specific options remain in `config/`; this guide covers the example and deployment-relevant project additions rather than every package option. `.env.testing` remains separate and must target isolated test resources.

## Deployment procedure

1. Install the locked Composer dependencies and check platform requirements as described in the [README](../README.md). Point the web root to `public/` and make `storage/` and `bootstrap/cache/` writable.
2. Prepare the deployment environment with the application/database settings and credentials for the capabilities actually used. Supply the new integration names; retain legacy fileserver URL/path settings until the client is updated. Preserve existing application and image encryption keys.
3. Back up the application database and review pending migrations through the deployment process. Do not use the demo seeders to initialize production or migrate the externally owned email-log database.
4. After installing the environment, run `php artisan config:clear`, then `php artisan config:cache` for deployment. These commands apply configuration; they do not make unsupported new variable names work. Avoid publishing configuration dumps containing secrets.
5. Verify staff login, Portal signed entry/return, geographic address lookup, a synthetic document upload and authorized fetch, and enabled notification/SMS delivery using agreed test recipients. Verify recovery SMTP and suppression-log connectivity separately. Confirm actual upstream requests when checking disabled capabilities, given the upload-existence-check limitation.

Future configuration changes should update `config/`, every consuming service, `.env.example`, this guide, and affected integration documentation together. Complete the fileserver authentication/URL client update before relying on its split endpoint/key settings.
