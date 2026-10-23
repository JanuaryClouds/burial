# Funeral Assistance System

Taguig City CSWDO application for managing funeral assistance requests, including Burial Assistance and Libreng Libing. Citizens enter through TLC Portal SSO; staff process applications through role-controlled workflows.

## Features and workflow

- Client, deceased beneficiary, family composition, and supporting document records.
- Interviews, assessments, recommendations, referrals, rejections, cancellations, and closure.
- Configurable assistance processing stages and workflow history.
- GIS forms, certificates, QR/barcode documents, PDF reports, and Excel exports.
- Portal citizen records, notifications, SMS, encrypted image storage, and geographic address lookups.

General flow: client and beneficiary registration → application → interview and assessment → recommendation or referral → assistance processing → closure. A **client** is the applicant/claimant; a **beneficiary** is the deceased person.

See [System Workflow](docs/architecture/flow.md) for business context. Some details describe earlier flows: staff login is currently `/admin/login`, and application processing is grouped under `/application`. Check the implementation before relying on historical URLs or stage counts.

## Requirements

The PHP lock file currently resolves Laravel 13 and Livewire 4. The UI uses Blade, Alpine.js, and bundled assets under `public/`.

The project runs within Hostinger's hosting restrictions and does not use Node.js or npm commands for setup, development, or deployment. Existing package and Vite configuration files are not part of the supported workflow.

- **PHP 8.4.1+ (64-bit)** for the locked development dependencies, including PHPUnit 13. The root `composer.json` PHP constraint alone does not reflect this requirement.
- **Composer 2** and the extensions checked by `composer check-platform-reqs --lock`. Include PDO MySQL for the default database and GD for image processing.
- **MySQL** for the default configuration. Other configured database drivers have not been verified for this project.
- Writable `storage/` and `bootstrap/cache/` directories.

Use `composer install` to preserve committed PHP dependency versions.

## Local setup

Run commands from the repository root after cloning. Examples use PowerShell, matching the Laragon development environment.

1. Create the environment file if it does not already exist:

   ```powershell
   Copy-Item .env.example .env
   ```

2. Edit `.env` with your local URL and a dedicated development database. Create that empty database in MySQL before migrating:

   ```dotenv
   APP_NAME="Funeral Assistance System"
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=burial_local
   DB_USERNAME=root
   DB_PASSWORD=

   ADMIN_PASSWORD=replace-with-a-local-development-password
   PORTAL_MOCK=true
   PORTAL_USERS_ENABLE_GET=false
   PORTAL_USERS_SEED_FROM_PORTAL=false
   SMS_ENABLE_POST=false
   PORTAL_NOTIFICATION_ENABLE_POST=false
   DISBURSEMENT_SYSTEM_ENABLE_POST=false
   ```

   `.env.example` is still the Laravel starter template. Add the project settings above yourself; integration settings are defined in [config/services.php](config/services.php). Keep credentials in your local environment file.

3. Install dependencies and generate the application key:

   ```powershell
   composer install
   composer check-platform-reqs --lock
   php artisan key:generate
   ```

4. Initialize the dedicated local database:

   ```powershell
   php artisan migrate
   php artisan db:seed
   ```

   The default seeder creates configuration, roles, permissions, workflow stages, and randomized demo records. It creates `superadmin@example.org` and sample staff accounts using `ADMIN_PASSWORD`. Set that variable before seeding. This is a development/demo dataset, not a production initialization procedure. Repeated seeding against existing data has not been verified as safe.

5. Run the application:

   ```powershell
   php artisan serve
   ```

   Open `http://127.0.0.1:8000/admin/login` for staff login. With `PORTAL_MOCK=true`, the landing page provides local citizen entry links from seeded users. Mock access is for local development. For a Laragon virtual host, point its document root at `public/` and set `APP_URL` to its URL.

## External services

Local data and mock citizen access do not make every feature self-contained. Obtain development endpoints and credentials from the maintainer for the flows you need.

| Service | Environment settings | Purpose |
| --- | --- | --- |
| TLC Portal / SSO | `PORTAL_ENDPOINT`, `PORTAL_USERS_ENABLE_GET`, `PORTAL_USERS_ENDPOINT`, `PORTAL_USERS_API_KEY`, `SSO_SECRET_KEY` | Citizen lookup and signed portal entry |
| Portal notifications | `PORTAL_NOTIFICATION_ENABLE_POST`, `PORTAL_NOTIFICATION_ENDPOINT`, `PORTAL_NOTIFICATION_API_KEY` | Citizen notifications |
| SMS | `SMS_ENABLE_POST`, `SMS_ENDPOINT`, `SMS_API_KEY` | SMS delivery |
| File server | `FILESERVER_ENABLE_GET`, `FILESERVER_ENABLE_POST`, `FILESERVER_ENDPOINT`, `FILESERVER_API`, `FILESERVER_ENCRYPTION_KEY` | Image retrieval and encrypted uploads |
| PSA classification | `PSA_CLASSIFICATION`, `PSA_CLASSIFICATION_ENDPOINT`, `PSA_CLASSIFICATION_VERSION`, `PSA_CLASSIFICATION_API_KEY` | Geographic address options |
| Disbursement | `DISBURSEMENT_SYSTEM_ENABLE_GET`, `DISBURSEMENT_SYSTEM_ENABLE_POST`, `DISBURSEMENT_SYSTEM_ENDPOINT`, `DISBURSEMENT_SYSTEM_API_KEY` | Disbursement integration configuration |

Image upload code requires a file server, encryption key, and an authenticated user token. Disabling integration flags does not provide a complete local upload replacement. PSA address lookups call an external API; disabling a flag does not supply local address data. Some configured integrations may be incomplete or unused in current flows.

After changing environment settings, clear cached configuration with `php artisan config:clear`.

## Project map

| Location | Responsibility |
| --- | --- |
| `routes/web.php`, `routes/*.php` | Entry points and routes grouped by feature |
| `app/Http/Controllers/` | HTTP actions and page entry points |
| `app/Livewire/`, `app/Livewire/Forms/` | Interactive screens and form state |
| `app/Services/` | Business operations, reports, and external clients |
| `app/Models/`, `app/Policies/` | Eloquent relationships and authorization |
| `app/Http/Requests/` | Request validation |
| `resources/views/` | Blade screens, components, and PDF templates |
| `database/migrations/`, `database/seeders/` | Schema, reference data, and demo data |
| `tests/` | PHPUnit unit and feature tests |
| `docs/architecture/flow.md` | Business workflow reference |

Application records connect clients and beneficiaries to assessments, recommendations, referrals, and outcomes. Many domain models use UUIDs. Workflow stages and permissions are seeded in `database/seeders/WorkflowStageSeeder.php`; the historical stage count in the workflow document is not authoritative.

## Development and verification

```powershell
composer validate --no-check-publish
composer check-platform-reqs --lock
composer larastan
```

`composer larastan` runs the level-5 PHPStan/Larastan configuration in `phpstan.neon`. Frontend changes use the project's existing Blade templates and public assets without a Node.js build step.

For a minimal test that does not boot Laravel or use the database:

```powershell
php vendor/phpunit/phpunit/phpunit --no-configuration --bootstrap vendor/autoload.php --do-not-cache-result tests/Unit
```

The unit suite currently contains only the starter assertion; passing it does not verify application behavior.

Before running feature tests, create a separate testing database and configure `.env.testing` with its connection details and an application key. Confirm it targets that testing database. `phpunit.xml` does not enable an in-memory database, and some tests create users without automatic database reset. Disable outbound notification, SMS, and disbursement calls in the testing environment.

```powershell
php artisan migrate --env=testing
php artisan db:seed --env=testing
php artisan test
```

Feature tests cover beneficiary family composition rendering and a live PSA region lookup. The disbursement test body is commented out and does not validate disbursement behavior. The PSA test needs a configured, reachable service. The suite is not a comprehensive regression check.

### Verification recorded on October 23, 2026

- `composer validate --no-check-publish`: passed.
- `composer check-platform-reqs --lock`: passed on PHP 8.4.5 (64-bit).
- Isolated unit command above: 1 test and 1 assertion passed, with 1 PHPUnit deprecation notice.
- Fresh-clone setup, migrations, seeders, feature tests, and static analysis were not executed during this documentation update. Setup remains to be verified against an isolated database and development service endpoints.
