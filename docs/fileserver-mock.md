# Local fileserver

The development mock runs as a separate PHP HTTP server. It stores and returns
opaque bytes; **Burial encrypts and decrypts images**, and keeps the encryption
key. The mock never connects to a database or to the production fileserver.
It models an assumed API, not verified behavior of the real server.

## Setup

In your local `.env`, use:

```dotenv
APP_ENV=local
FILESERVER_MOCK=true
FILESERVER_MOCK_PORT=8099
FILESERVER_MOCK_TOKEN=replace-with-a-local-only-secret
FILESERVER_MOCK_DUPLICATES=reject
FILESERVER_MOCK_MAX_UPLOAD_KB=32768
FILESERVER_ENABLE_GET=true
FILESERVER_ENABLE_POST=true
FILESERVER_ENCRYPTION_KEY=base64:replace-with-a-base64-encoded-32-byte-key
```

Generate a local mock token with `php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"`.
Generate a **development** encryption key with
`php -r "echo 'base64:', base64_encode(random_bytes(32)), PHP_EOL;"`.
Keep existing keys if you need to read previously encrypted files. Do not run
`key:generate` for this: the fileserver key is separate from `APP_KEY`.
Keep both secrets in `.env`; never commit them.

If configuration was cached, run `php artisan config:clear`. Start the server in
a second terminal:

```powershell
php artisan fileserver:mock
```

Keep your normal Laragon application running. The mock always binds to
`127.0.0.1`, on the configured port; Ctrl+C stops it. Stop it before changing its
token, port or duplicate policy, then start it again. No Node tooling is needed.
The application and mock must run on the same machine/network namespace.

Mock mode ignores `FILESERVER_ENDPOINT` and `FILESERVER_API`. Enabling mock mode
in an environment other than `local` or `testing` fails rather than contacting
production. Uploads still require a logged-in, writable Burial account, but do
not need a personal access token. Existing enable flags apply: disabled uploads
retain their existing skip-and-log behavior; disabled downloads now return 404
without a network request, rather than requesting a remote `test.png`.

## API and storage

| Request | Behavior |
| --- | --- |
| `POST /upload` | Multipart `token` and `file`; static local token; 201 JSON with `status: success` and `filename` |
| `GET /burial/{filename}` | Original bytes as `application/octet-stream`, or 404 |

The mock does not inspect image content or decrypt anything. The maximum file
size defaults to 32 MiB. `FILESERVER_MOCK_MAX_UPLOAD_KB` accepts 1–32768 KiB;
lower it to simulate oversized uploads. Filenames must start with an ASCII letter or digit, contain only
letters, digits, underscores, periods or hyphens, and be at most 200 characters.
Errors use JSON with `status: error`: 400 invalid upload/name, 401 invalid token,
409 duplicate, 413 oversized upload, 500 storage/configuration failure.
Downloads are deliberately unauthenticated, matching the reported external API;
the mock is a development tool, not a deployable storage service.

Storage is `storage/app/private/fileserver-mock`, excluded from Git. It is outside
the mock document root. Duplicate writes reject by default. To experiment with
replacement via Postman, set `FILESERVER_MOCK_DUPLICATES=overwrite` and restart.
Burial's own existence check continues rejecting duplicates in either mode.
All reads, writes and cleanup share a lock; readers never receive partial writes.

Files older than seven days are removed on startup and before authenticated
uploads, not by a background timer. To inspect or run cleanup explicitly:

```powershell
php artisan fileserver:mock-cleanup --dry-run
php artisan fileserver:mock-cleanup
php artisan fileserver:mock-cleanup --hours=0 --dry-run
```

Only valid filenames directly inside the mock directory are considered; links,
nested directories and internal lock/temp files are excluded. `--hours=0` without
`--dry-run` removes all mock files older than the current second. This never
deletes remote files. Cleanup only runs in local/testing environments.

## Image encryption and viewing

New `.jpg.enc` uploads contain `BURIAL-IMAGE-V1` plus a newline, followed by
Laravel's AES-256-GCM encrypted-string envelope. That authenticates the IV,
ciphertext and tag. Existing binary AES-CBC files remain readable by verifying
their ciphertext HMAC before decryption. Their historical unauthenticated-IV
weakness remains; they are not rewritten automatically. Plain, unencrypted
remote JPEGs are not accepted as encrypted documents.

The Burial image endpoint authorizes the application before contacting storage,
then decrypts and returns JPEG bytes with private/no-store and nosniff headers.
Missing/disabled images return 404. Connection failures, upstream errors and
corruption return a generic 502. The image window uses the existing view.
The Documents gallery fetches each image once and displays its viewer only after
successful decoding. Missing images hide the entire grid slot; service failures
show an unavailable message instead of a broken image. If every image is missing,
the gallery displays an empty state. No inline script handlers or additional
server-side existence probes are used.
Uploads have no automatic retries because a timed-out request might have stored
the file already. A failed existence check stops uploading unless it was a 404.

## Manual checks

1. Use a synthetic image and an authorized local account to submit an application.
2. Open its documents and separate image window; both should display the image.
3. Fetch the corresponding mock URL directly. It should return encrypted bytes,
   not a viewable image. Local filenames start with `test-`.
4. Try the Burial image URL as an unrelated account; access must be denied.
5. Stop the mock; existing images should return 502. Restart it to recover.
6. Upload the same filename directly with Postman to test reject/overwrite modes.

Automated verification:

```powershell
php artisan test --compact tests/Feature/ImagePayloadTest.php tests/Feature/MockFileserverTest.php tests/Feature/ImageDeliveryTest.php
```

Tests use temporary mock storage, ephemeral loopback ports and an isolated SQLite
database where needed, not shared records or production credentials.
HTTP size-boundary tests use a lowered 32 KiB limit. A full 32 MiB upload timed out
on the current Windows environment after transmitting its body, so that size has
not been verified end to end here.

The isolated viewer browser fixture uses synthetic responses, no database and an
enforced CSP. Run `php -S 127.0.0.1:8108 -t tests/browser tests/browser/image-viewer-router.php`
and open `http://127.0.0.1:8108/`. It checks successful/missing/unavailable images,
one request per viewer, navigation reinitialization, popup dispatch and removal
of pending viewers. Stop the fixture server with Ctrl+C.

## Returning to the real fileserver

Stop the mock, set `FILESERVER_MOCK=false`, restore the remote endpoint/API settings
and enable flags, and clear cached configuration if necessary. The remote token
hash-plus-date protocol remains unchanged. Local mock files are not migrated.
Preserve the encryption key associated with any real files. Remote acceptance of
`.enc` files and byte preservation still require integration verification.
