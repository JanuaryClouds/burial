# Workflow stage custom fields

Superadmins configure fields inside each stage on the workflow editor. Supported
types are text, number, date, and password. Fields default to required. Machine
names are generated when the label is first entered, remain stable on label
changes, and may be edited explicitly. Names must start with a lowercase letter
and contain only lowercase letters, digits, and underscores (maximum 64 characters).
Names are unique per stage. Field order follows the draft list.
Fields can also have optional plain-text help text (up to 1,000 characters),
shown below the processing input through its `helpText` prop. Omitted, false,
empty, or whitespace-only help text is stored as `null` and stays hidden.
Help text is part of the versioned definition; changing it publishes a new version.

## Publication and processing

Draft fields are unsaved until **Save New Version**. Publication clones the full
definition, including fields, with new stage and field UUIDs. Field-only edits
create a version; saving an unchanged definition does not. Historical definitions
are read-only. Recommendations continue using their assigned workflow version.

`WorkflowVersionService::publish()` accepts `stages[*].custom_fields`, containing
`name`, `label`, optional `help_text`, `input_type`, and `required`. Array order determines `order_no`.
An omitted field list means an empty list for that stage; callers publishing a
complete snapshot must include every field they intend to retain.

`WorkflowHistoryService::store()` accepts `custom_values`, keyed by the target
stage's field UUIDs. Values are strings: text/password up to 255 characters,
signed decimal numbers without exponent notation, and dates in `YYYY-MM-DD`
format. Decimal values are retained as strings to preserve precision. Unknown
or foreign-stage field UUIDs are rejected. Required fields must be present;
empty optional values create no value record. Recording history, values,
remarks, and stage advancement shares one transaction.

Every stage visit has its own immutable values. Returning to a stage starts
with blank fields. History details show the labels and values from the original
workflow version, with application-view authorization checked server-side.
Password inputs conceal entry, store recoverably encrypted values, and show
plaintext to authorized history viewers. Preserve and back up `APP_KEY` securely;
changing it without retaining access to the original key makes these values
unreadable. Values are excluded from automatic model serialization and are not
included in success activity logs. Custom input arrays are excluded from flashed
validation inputs.

## Defaults

Fresh Funeral Assistance workflow publication seeds three required definitions:

| Stage | Name | Label | Type |
| --- | --- | --- | --- |
| Budget | `obr_number` | OBR Number | text |
| Releasing | `check_number` | Check Number | text |
| Releasing | `check_available_date` | Date Availability of Check | date |

Reseeding does not update existing workflow definitions. On existing installations,
add these fields through the workflow editor and publish a new version. Existing
runs retain their original fields. Demonstration history seeders/factories provide
sample values for required definitions; these are not production finance data.

## Deployment

`2026_10_26_100000_add_help_text_to_custom_fields.php` adds nullable `help_text`
without changing existing definitions. It runs as part of fresh migrations and
can also be applied as a normal forward migration on an existing installation.
Clear/rebuild compiled views when deploying the updated input component
(`php artisan view:clear`) so its new help-text constructor prop is recognized.

The forward migration is
`database/migrations/custom_fields/2026_10_25_100000_prepare_versioned_stage_custom_fields.php`.
It changes polymorphic owner IDs from integers to 36-character strings, preserving
numeric owners while supporting UUIDs. It widens values to text, adds owner/name
and owner/field uniqueness, and restricts deletion of referenced definitions.
Original migrations are unchanged. Duplicate owner/name definitions, duplicate
owner/field values, or orphaned definition references stop migration before DDL;
resolve these records explicitly rather than deleting them automatically.

Apply only during an explicitly approved maintenance window:

1. Confirm workflow versioning migrations and their preflight requirements are
   satisfied; run the read-only `php artisan workflows:audit-versions`.
2. Back up the database and preserve the application encryption key. Verify a
   restore procedure. Pause web writers, queue workers, scheduled writers, and
   other integrations that can write to these tables.
3. Deploy the code and run the approved pending migrations with
   `php artisan migrate --force`. Check `php artisan migrate:status` before
   selecting the batch: this command applies all pending registered migrations.
4. Verify workflow editing, publication, required field validation, historical
   details, and continued processing of an older pinned recommendation. Resume
   writers after verification.

MySQL schema alterations are not transactional. If a DDL failure occurs, inspect
the partially applied schema before retrying. Automatic `down()` is intentionally
blocked because UUID owners and encrypted values cannot fit the original columns.
Rollback requires a reviewed database backup restore together with compatible
application code; do not narrow columns or delete recorded values.

This feature does not import historical finance information, synchronize the
cheques table, change finance reports/exports, add configurable masks, or permit
post-save value corrections. No Node.js or npm commands are required.

## Verification

The workflow tests explicitly use isolated SQLite databases, including a temporary
SQLite file for the concurrency test:

```powershell
php vendor/phpunit/phpunit/phpunit tests/Feature/WorkflowEditingTest.php tests/Feature/WorkflowCustomFieldMigrationTest.php
```

The schema migration is exercised with legacy rows, duplicate conflicts, UUID
owners, uniqueness constraints, and restrictive foreign keys. These tests do not
apply migrations to the configured shared application database. Validate the
forward migration on a disposable MySQL copy before the production window.
