# Testing and verification

## Required behavior

1. Use the `testing-best-practices` skill and nearby test conventions. Cover changed behavior and meaningful failure modes; avoid tests that merely repeat implementation text or test framework internals. Copy/style-only changes do not require application tests.
2. Run the narrowest tests covering the change, expanding only for affected interactions or unresolved failures. Routine test updates within the authorized task need no separate approval; preserve distinct coverage and never weaken a test simply to obtain a passing run.
3. Before running database-backed tests, verify that their connection is disposable and isolated. `APP_ENV=testing` alone is insufficient: `phpunit.xml` does not globally force an in-memory SQLite database. Follow the affected suite's explicit connection setup and inspect `.env.testing` safely when needed; do not print secrets.
4. Never run migrations, destructive refreshes, demo seeders, or integration cleanup against a shared or production database as verification. Explicit MySQL/concurrency modes must use their documented disposable databases.
5. Fake or isolate outbound services. Real SMTP, SMS, Portal, fileserver, and shared-log checks require agreed destinations and test resources. Mock success does not prove live integration compatibility.
6. Use the configured `composer larastan` analysis (currently level 5), not a skill-imposed level 9 or an invented suite-wide coverage percentage. Separate existing failures from regressions; do not claim a check passed when it was blocked or not run.
7. Follow the project's Pint requirement for PHP changes while preserving unrelated working-tree edits. If dirty-file formatting would modify unrelated work, scope formatting to affected PHP files and explain the scope.
8. Report checks and material limits: SQLite does not establish MySQL locking behavior, and server-side tests do not establish browser or Hostinger behavior.

## Existing test facilities

- [Base TestCase](../../tests/TestCase.php) makes `actingAs` represent a completed login, including session setup and superadmin MFA proof. Use the actual login flow or remove that proof when testing pre-authentication or MFA boundaries.
- [PHPUnit configuration](../../phpunit.xml), [static analysis configuration](../../phpstan.neon), and [README verification guidance](../../README.md).
- Integration checks are linked from [mail](mail.md), [fileserver](fileserver.md), and [workflow transitions](workflow-transitions.md).
