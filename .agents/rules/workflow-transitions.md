# Application processing and workflow transitions

## Context

Applications retain claimant-assignment cycles and recommendations pinned to workflow versions. Published workflow changes must not rewrite earlier runs or recorded fields. These rules summarize existing service contracts to preserve; they do not authorize a data migration.

## Required behavior

1. Use existing processing, recommendation, claimant replacement, and workflow services. Recheck authorization and application state server-side for every write; hidden UI controls are not authorization.
2. Preserve transactional locking and stale-form checks for the expected claimant assignment, recommendation, and current stage. Reject stale writes rather than applying them to a new claimant or run.
3. Keep the current claimant cycle's interview and assessment prerequisites. Terminal outcomes, paused workflows, or inconsistent stage references must not be bypassed to force a transition.
4. Select transition targets from the recommendation's pinned workflow version. Current processing permits the next stage or an earlier stage; backward transitions require a reason. Preserve the target-stage permission check and the superadmin path, subject to state restrictions.
5. Record stage history, dates, validated custom values, remarks, and advancement atomically. Date out cannot precede date in. Returning to a stage creates a new visit; do not overwrite previous visit values.
6. Publish a new workflow version for definition changes, including custom fields and help text. Historical definitions remain read-only; existing recommendations keep their assigned version. Do not assume fixed stage counts or hardcode mutable stage IDs.
7. Preserve custom-field validation against the target stage/version. Password-type values are recoverably encrypted, visible only through authorized history views, and excluded from automatic serialization, success logs, and flashed inputs. Preserve `APP_KEY`.
8. Claimant replacement preserves application identity, beneficiary, documents, and history, but starts a new claimant cycle with its required interview, assessment, recommendation, and workflow run. Respect replacement limits and payment/terminal-state restrictions. Do not replace claimants by directly updating `applications.client_uuid`.
9. Former claimants cannot mutate the application. They retain the citizen-facing view and documents, not internal assessments, recommendations, or staff-only remarks. See [business actors](business-actors.md).
10. Interview scheduling requires successful required notifications; notification failure must not leave a successfully scheduled interview. In production, disabling a required notification channel also blocks scheduling. See [SMS and interview notifications](sms.md) for failure, partial-send, and local/testing constraints.

## References

- [Processing](../../app/Services/ApplicationProcessingService.php), [workflow history](../../app/Services/WorkflowHistoryService.php), [workflow versions](../../app/Services/WorkflowVersionService.php), and [claimant replacement](../../app/Services/ClaimantReplacementService.php).
- [Claimant replacement](../../docs/claimant-replacement.md), [custom fields](../../docs/architecture/workflow-custom-fields.md), and [access roles](../../docs/architecture/access-roles.md) contain detailed contracts and focused checks.
- Schema changes require reviewed deployment procedures. Read-only audits and migration instructions in those documents do not authorize applying changes to shared databases.
