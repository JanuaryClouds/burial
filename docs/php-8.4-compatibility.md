# PHP 8.4.27 compatibility

The shared Hostinger target is PHP **8.4.27, 64-bit**. The project previously required PHP `^8.5.12`, preventing installation on that host even though the locked packages support PHP 8.4.27.

`composer.json` now requires `^8.4.27` and sets `config.platform.php` to `8.4.27`. This keeps dependency resolution compatible with the shared host when developers run a newer PHP version. It does not install PHP or simulate extensions. `composer.lock` was refreshed without changing any package versions.

## Locked package requirements

The compatibility check on November 19, 2026 included all 152 locked runtime and development packages. Notable packages are:

| Package | Locked version | Declared PHP requirement |
| --- | --- | --- |
| `laravel/framework` | 13.40.0 | `^8.3` |
| `maatwebsite/excel` | 4.0.6 | `^8.3` |
| `phpoffice/phpspreadsheet` | 5.10.0 | `^8.2` |
| `phpunit/phpunit` | 13.4.6 | `>=8.4.1` |

No downgrades or vendor patches were needed. The locked PhpSpreadsheet version accepts PHP 8.4.27. Its extension requirements must also be satisfied on Hostinger; see the [upstream requirements](https://phpspreadsheet.readthedocs.io/en/latest/).

## Code and verification

Verification uses an isolated official Windows PHP 8.4.27 CLI runtime; the default Laragon PHP installation is unchanged. Feature tests use SQLite in memory instead of the shared MySQL database.

- Composer installation, strict manifest validation, and all locked platform requirements pass on PHP 8.4.27.
- Composer reports no package that prohibits PHP 8.4.27 and no security advisories during the lock refresh.
- Laravel package discovery and application route registration pass.
- All 880 PHP and Blade files checked under the application, bootstrap, configuration, database, routes, tests, resources, and public directories pass PHP 8.4.27 syntax checks. The new spreadsheet test also passes Pint's formatting check.
- `tests/Unit/SpreadsheetCompatibilityTest.php` exercises the actual application export template through XLSX loading, writing, and reloading, checking sample text, amount, status, and number format preservation. It does not need database records or change the template.
- Full PHPUnit run on PHP 8.4.27: **323 tests, 2,112 assertions; 320 passed, two skipped, one error**. The error is `SharedEmailStandardsTest::test_mailables_and_notifications_share_the_global_policy`, whose mail rendering queries a missing `sexes` table in the in-memory default database. The same error was reproduced on PHP 8.5.12, confirming it is unrelated to this PHP version adjustment. The isolated spreadsheet test passes with six assertions.
- PHPUnit emitted one runner deprecation for the verification command's `--do-not-cache-result` option; PHPUnit 13 recommends `--do-not-record-test-run-history` instead. This was not a PHP runtime deprecation.
- Review against PHP's [8.5 additions](https://www.php.net/manual/en/migration85.new-features.php) found no application dependency on PHP 8.5 syntax or newly introduced functions. The database configuration's `Pdo\Mysql` subclass is available in PHP 8.4 with `pdo_mysql` enabled; see the [PHP 8.4 documentation](https://www.php.net/manual/en/migration84.new-features.php).
- PHPStan/Larastan runs on PHP 8.4.27 but reports 373 existing findings, including missing legacy model classes, relationship/type issues, and malformed PHPDoc. Application source and package versions were not changed by this compatibility adjustment. Static analysis is not a passing project-wide check.

## Shared-host verification

After selecting PHP 8.4.27 for the website, ensure the shell used for Composer also runs that version:

```sh
php -v
composer install --prefer-dist --optimize-autoloader
composer check-platform-reqs --lock
```

For a deployment that omits development tools, add `--no-dev` to both Composer commands. Preserve the committed lock file and run installation without `--ignore-platform-reqs`.

Enable the extensions reported by Composer, plus `pdo_mysql` for the configured MySQL database and GD for image operations. Website PHP and SSH/cron PHP can use different configurations, so verify both. This compatibility change does not require a database migration.

The Hostinger runtime, browser flows, live MySQL behavior, and remote integrations still require verification on the host. Local CLI checks do not establish that its PHP extensions or server configuration match.
