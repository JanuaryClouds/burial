# Accessory Word documents

Superadmins manage named DOCX templates on **Edit Funeral Assistance Type**, independently of the type's Save button. Each type supports any number of templates. Replacing a template retains its identity; removing it disables its download immediately.

CSWDO users and superadmins use **Show Application → Printable Documents**, below workflow history, for GIS/certificate PDFs and accessory Word downloads. The generic staff role and departmental workflow roles do not grant printing access. Accessory documents use the current recommendation's assistance type, current application details, and latest uploaded template at download time. No historical output snapshots are retained. Staff can make final edits in Microsoft Word. See [access roles](access-roles.md) for rollout and assignment details.

## Authoring templates

Use ordinary Word text, table cells, headers, or footers. Enter placeholders exactly, for example `${client.fullname}` or `${beneficiary.date_of_birth}`, with consistent formatting within each placeholder. The edit screen lists the complete supported catalog from `DocumentPlaceholderRegistry`.

Names follow the existing `fullname()` methods, including middle initials. Separate name-part placeholders preserve full middle names. Dates use `October 27, 2026`, amounts use `10,000.00`, and absent data becomes blank. Unsupported keys cause upload rejection. Templates may contain no placeholders. Repeating rows, images, conditional blocks, Word fields, and PDF conversion are outside this module.

Placeholders are a fixed PHP allowlist. New mappings require a system update to the registry, including their eager-loaded relationships and tests. Templates never execute PHP or Blade.

## Deployment and operations

- Deploy the code and Composer dependencies, then apply `2026_10_27_120000_create_document_templates_table.php` through the normal approved migration process. Back up the database first; implementation does not authorize a shared-database migration.
- PHP needs ZIP, DOM/XML, Fileinfo, and writable private storage. The `document_templates` disk uses `storage/app/private/document-templates`; do not expose it through a web-server alias or storage symlink. No Node/npm or LibreOffice is required.
- Upload limit: 5 MB, maximum 2,000 ZIP entries and 50 MB expanded content. Hostinger/PHP upload and POST limits must allow the upload plus request overhead.
- Files are generated only on download. The `generated` directory holds populated DOCX files temporarily while they are delivered, with unique paths and deletion after delivery or generation failure. These are not saved historical outputs. Abrupt process termination can leave a file behind.
- Back up the template files together with the database. Cleanup failures are logged with generated storage paths; generation errors log template identity and exception class without populated values.
- Focused verification: `php vendor/phpunit/phpunit/phpunit tests/Feature/DocumentTemplateTest.php`. The test configures its own in-memory SQLite connection and uses the project's migration loader; it does not migrate the configured application database.

Before release, open a generated copy of each real staff template in Microsoft Word and check its layout with realistic long names and addresses. A generated sample only verifies the processing mechanism, not every uploaded layout.

## Orphan cleanup

Preview orphaned documents with `php artisan documents:cleanup --dry-run`, then remove them with `php artisan documents:cleanup`.

The command scans only DOCX files inside the private document disk's `templates` and `generated` directories. It preserves every path referenced by a `document_templates` record, including templates belonging to soft-deleted assistance types. An uploaded template is not orphaned just because its assistance type has no recommendations. It removes only unreferenced files at least 24 hours old by default, allowing recent uploads and downloads to finish. It does not delete database records or inspect other application uploads.

Use `--hours=48` to change the age threshold. For immediate testing cleanup, use `--hours=0 --dry-run` followed by `--hours=0` only after stopping uploads and downloads; zero removes the protection for in-progress files. The command stops without deleting anything if it cannot initially read database references or list storage. Individual inspection/deletion failures are reported with a nonzero exit status.
