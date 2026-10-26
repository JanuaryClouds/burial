# Claimant replacement

Superadmins can follow **Change Claimant** from an active application's summary to a dedicated page. That page displays the application summary and replacement form separately and does not poll while the form is edited. The new claimant must already have an account and a complete client draft that has never been assigned to an application. Choose their relationship to the recorded beneficiary and enter a reason, then confirm the reset.

The system setting **Claimant changes allowed per application** defaults to 1; 0 disables changes. Increasing it permits further replacements. Reducing it never removes history. Issued cheques, claimed benefits, and terminal application outcomes prevent replacement.

Replacement preserves the tracking number, QR code, beneficiary, files, and previous processing records. The new claimant requires a new interview, assessment, recommendation, and workflow run. Each claimant cycle requires one completed interview before assessment and recommendation. Interview scheduling stops after completion or workflow assignment. Existing documents must be reviewed by staff. Both current and former claimants retain the client-facing application view and documents; former claimants cannot cancel or modify it. Internal assessments, recommendations, and remarks retain staff access rules.

## Deployment

The new code requires the claimant-assignment migration and the existing workflow-version migrations. Deploy code and schema together while writers are paused. Do not enable the new code against an unmigrated database.

1. Obtain approval for shared-database rollout and take a verified database backup, including encrypted identity snapshots and the existing application encryption key.
2. Pause application, interview, assessment, recommendation, outcome, and payment writers.
3. Run `php artisan applications:audit-claimants`. This is read-only. Resolve reported ambiguous mappings, duplicate assessments, invalid workflow references, or incompatible cheque schema before proceeding. The migration repeats the preflight before DDL.
4. Apply migrations only within the approved maintenance window. Existing applications receive initial assignments; their existing processing records are attached without deletion. Unused drafts' interviews remain unassigned.
5. Clear deployment caches with `php artisan optimize:clear`, then run `php artisan applications:audit-claimants` again. Verify current pointers, application visibility, settings, and a replacement in a nonproduction copy before resuming writers.

Backfilled snapshots represent identity at migration time; they cannot reconstruct past edits. Ordinary rollback is refused after replacements exist because it would discard audit history. Restore a verified backup if a rollback is necessary.

## Verification and integrations

Run `php vendor/phpunit/phpunit/phpunit --filter ClaimantReplacementTest --do-not-cache-result` for isolated SQLite behavior and migration tests. Run with `CLAIMANT_TEST_MYSQL=1` for MySQL validation. That mode creates and removes uniquely named `burial_claimant_test_*` databases; it does not migrate the configured application database. The MySQL concurrency test starts independent PHP processes to race competing replacements and consumption of one draft.

Payment integrations must call `ChequeService::save` with the application UUID and expected claimant-assignment UUID. Processing integrations must similarly supply the assignment captured when their form/job was opened. Direct SQL changes to `applications.client_uuid` are unsupported: they bypass the reset and audit trail. The post-deployment audit detects inconsistent claimant pointers.

This workflow uses PHP and Composer tools and requires no Node.js or npm commands.
