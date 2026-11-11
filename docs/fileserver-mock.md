# Local fileserver

The standalone PHP mock binds only to loopback, runs only in local/testing, and
never connects to a database or the real fileserver. Burial owns image encryption
and decryption. This mock tests the new request contract, not upstream encryption
compatibility.

## Setup

```dotenv
FILESERVER_MOCK=true
FILESERVER_MOCK_PORT=8099
FILESERVER_MOCK_TOKEN=replace-with-a-local-only-secret
FILESERVER_MOCK_DUPLICATES=reject
FILESERVER_MOCK_MAX_UPLOAD_KB=30720
FILESERVER_GET_ENABLE=true
FILESERVER_POST_ENABLE=true
FILESERVER_ENCRYPTION_KEY=base64:replace-with-a-base64-encoded-32-byte-key
```

Preserve existing encryption keys to read existing documents. Keep secrets in
`.env`. If configuration is cached, run `php artisan config:clear`, then start
`php artisan fileserver:mock` in a second terminal. Restart after config changes.
The application and mock must use the same timezone (`config('app.timezone')` is
passed to the server). Standalone router launches default to Asia/Manila.

## Request contract

Every request uses `/apiburial/` and requires `token = YYYYMMDD + mock secret`,
including view, download, and list. Missing tokens return 401; incorrect or expired
tokens return 403. Old `/upload` and `/burial/{filename}` routes return 404.

| Method | action | Other parameters | Response |
| --- | --- | --- | --- |
| POST multipart | upload | token, optional folder, file | 200 JSON: status, file, folder, reencoded |
| POST JSON | upload | token, optional folder, file_name, file_data | Same; file_data is strict Base64, optionally a Base64 data URI |
| GET | view | token, optional folder, file | Bytes with inline disposition, or 404 |
| GET | download | token, optional folder, file | Bytes with attachment disposition, or 404 |
| GET | list | token, optional folder, optional limit | status, folder, count, total_count, files |
| POST JSON/form | delete | token, optional folder, file | 200 JSON, or 404 |

Folders are single directory names, case-sensitive where the host filesystem is;
use consistent lowercase names such as `deathcertificates`. Folder and file names
must start with an ASCII letter/digit, contain only letters, digits, underscores,
periods or hyphens, and be at most 200 characters. Paths, traversal, symlinks, and
internal files are excluded. Listing is sorted by name; limit defaults to 100 and
accepts 1–1000. Count is the number returned; total_count is the full folder count.
Invalid requests, extensions, Base64, and uploads over 30 MiB return 400.
The configurable limit accepts 1–30720 KiB. Duplicates return 409 by default;
`FILESERVER_MOCK_DUPLICATES=overwrite` permits direct API replacement.

## Explicit differences from the real server

The mock preserves bytes unchanged, accepts `.enc` in addition to jpg/jpeg/png/
webp/pdf, preserves submitted filenames, and returns `reencoded: false`.
The reference shows timestamp-prefixed filenames and image re-encoding but does
not fully specify naming, extension policy, or byte preservation. Real `.enc`
acceptance remains unverified. Mock success is not evidence of remote support.
There is no optional encryption switch or plaintext fallback.

Burial uses the same authenticated upload/download contract for mock and remote
storage. `FILESERVER_MOCK` chooses the destination; GET/POST flags control the
operations. Both submission flows resolve server-side `DocumentRequirement`
records and use `document-requirement-{id}` folders with tracking-number filenames.
Folders are single-level and stable across display-name edits.

The `application_document_files` table records the actual returned filename and
folder. Apply its new migration through the reviewed deployment process before
using document uploads/fetching. Mock and remote references are separate. If no
reference exists, fetching tries the requirement folder and then the historical
flat filename on 404 only. A saved reference is authoritative: a missing file
there does not fall back to another location. Legacy flat files still depend on
their original requirement labels; no files are automatically moved.

For remote configuration, see the [deployment guide](deployment-environment.md#fileserver).
The mock's deterministic names do not establish the real server's naming behavior;
remote timestamp-prefixed names are supported through saved references.
Browser image routes still authorize the application and proxy decrypted JPEGs
with private/no-store and nosniff headers; tokens stay server-side. Missing files
return 404, upstream failures/corruption return generic 502. Disabled uploads keep
the existing skip-and-log behavior. Uploads are not automatically retried.

## Storage and cleanup

Files live under `storage/app/private/fileserver-mock`, outside the document root.
Reads, writes, deletes and listings use storage locks. Cleanup includes valid
folders, skips links and internal files, and removes files older than seven days
on startup and before authenticated uploads. Empty folders are retained.

```powershell
php artisan fileserver:mock-cleanup --dry-run
php artisan fileserver:mock-cleanup
php artisan fileserver:mock-cleanup --hours=0 --dry-run
```

Cleanup runs only locally/in tests and never touches remote files. Review dry-run
output before intentionally clearing mock documents.

## Verification

```powershell
php artisan test --compact tests/Feature/ImagePayloadTest.php tests/Feature/MockFileserverTest.php tests/Feature/ImageDeliveryTest.php
```

Tests use temporary storage, ephemeral loopback ports, synthetic documents and
isolated SQLite where required. Size-boundary tests lower the limit to 32 KiB;
they do not establish full 30 MiB end-to-end support. Production credentials are
unnecessary; reference metadata tests use isolated SQLite. To return to the real service, stop the mock, set
`FILESERVER_MOCK=false`, and configure the new remote API URLs/system secrets while preserving encryption keys.
