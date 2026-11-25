# Environment-aware submission and interview scheduling

## Environment policy

`local` and `testing` never send files to the real fileserver or call external SMS/Portal notification APIs, even when their POST flags are true. `FILESERVER_MOCK=false` skips image storage and waives required images. Selected files are discarded, with no document references. With the mock enabled, only loopback mock storage is used, required images are enforced, and an unavailable mock blocks submission. Mock GET/POST are independent of the real capability flags.

Production and other environments ignore the mock switch and never probe the mock health endpoint. Real GET and POST follow the existing configuration flags, which remain false by default. Disabled GET prevents document retrieval without independently preventing submission. Disabled POST blocks any submission needing required or selected images. Missing real-service configuration fails closed. JPEG conversion and encrypted payload formats are unchanged. Actual remote encrypted-byte compatibility still needs provider verification.

## Draft recovery

Citizen submissions now retain owner-bound `application_submission_drafts`, with selected client/beneficiary/relationship, a version, and encrypted staged document references. Walk-ins retain their existing encrypted intake document map. Successful uploads survive a later failure; retry selects only missing files. Final submission rechecks ownership, requirements and storage destination, then records references and the application in one database transaction. Old/stale or already-submitted drafts are rejected.

Database rollback cannot undo remote storage. Uploads are never automatically retried or deleted. If the connection dies after remote acceptance but before a reference is saved, contact the fileserver operator before deliberately resending; there is no provider idempotency guarantee. No permanent local copies are added.

## Interview recovery

`InterviewService::store` delegates to the scheduling coordinator. Requests and per-channel attempts are durable; sensitive recipient/message snapshots are encrypted using `APP_KEY`. Production Portal-linked clients require both Portal and SMS; accountless walk-ins require SMS only. All required channels must be enabled and configured. Local/testing skips both external channels and still records the linked citizen's internal notification.

Only after all required providers accept does the coordinator create a scheduled interview and its internal notification. External acceptance does not prove delivery or reading. A failed attempt leaves no scheduled interview. The form retains its slot and offers an authorized retry that skips accepted channels. Changing the slot starts a different request; accepted messages cannot be recalled.

A connection timeout is uncertain. An interrupted sending claim becomes uncertain after at least five minutes (or ten times the longest channel timeout). Staff must acknowledge duplicate risk before retrying an uncertain channel. No timed process or automatic retry runs. Active claims prevent concurrent sends. Permissions, claimant assignment, application state, slot and recipient snapshot are checked again on retries/finalization. External messages cannot be rolled back if state changes or final database commit fails.

Internal notification recording is separate from Portal posting. External flags never disable the system inbox, and failed interview attempts do not announce a confirmed interview internally.

## Deployment and verification

Deploy code and the forward migration `database/migrations/data/2026_11_25_082818_create_submission_and_interview_scheduling_tables.php` together through the reviewed maintenance process. It adds draft, scheduling-request, and attempt tables; existing applications and interviews are not backfilled or notified. Preserve `APP_KEY` and the separate fileserver encryption key. Do not apply this migration to a shared database during local implementation. Rolling it back discards new tracking data and requires a reviewed backup/maintenance window.

Use `php artisan test --compact tests/Feature/EnvironmentAwareSubmissionTest.php tests/Feature/InterviewSchedulingTest.php tests/Feature/WalkInFileserverTest.php` plus affected image, walk-in and lifecycle suites. These tests use faked HTTP and disposable SQLite. MySQL lock behavior, live providers and Hostinger deployment require separate verification. No Node build, queue worker, diagnostics panel or new dependency is required.
