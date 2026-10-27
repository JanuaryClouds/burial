# Internal, CSWDO, and workflow access

Roleless users are citizen clients. Any role identifies an internal user; citizen client, beneficiary, and application creation is denied to all internal users, including superadmins. This change does not resolve staff/client credential conflicts.

## Roles and permissions

| Role | Purpose |
| --- | --- |
| `staff` | Automatic CMS membership, without operational permissions |
| `CSWDO` | Office record indexes and printable documents |
| `Admin`, `Budget`, `Accounting`, `Treasury`, custom department roles | Explicitly assigned `Workflow …` permissions |
| `Releasing` | `Workflow Releasing` permission |
| `assessor`, `reporter`, `Closer`, custom action roles | Specific office action permissions; CSWDO membership is also required |
| `superadmin` | System/CMS and operational support, subject to processing-state restrictions; cannot enter citizen creation flows |

Assign CSWDO explicitly. CMS creation defaults to staff only, with optional operational role selection. The staff marker cannot be removed through CMS edits. `staff`, `CSWDO`, and `superadmin` cannot be renamed or deleted through ordinary role management. CSWDO membership itself does not grant interview, assessment, recommendation, report, referral, rejection, or closure permissions. Both inherited role permissions and direct permissions count.

`User` provides shared identity/office/action checks. `ApplicationAccessService` governs application reading. Livewire components reauthorize independently, including subsequent public actions and polling. Role and permission relationships are refreshed once per request and reused within that request.

## Departmental application access

Workflow users use **Search Application** or the search-focused dashboard. Search accepts an exact tracking number or the scanned `FUNERAL-…` code; inaccessible and missing applications return the same message. Departmental users do not receive record indexes or global dashboard statistics.

Reading requires a permission matching the current stage or a permitted next/return destination in the current recommendation's pinned workflow. Reading remains available when paused or terminal, while existing processing rules still prevent invalid writes. New logs retain stage UUID, workflow ownership, expected recommendation/current-stage/claimant, next-or-earlier transition, and backward-reason checks.

The departmental application view includes summary, status, assessment, recommendation, submitted documents, and current-run workflow history/form. It omits printable documents, standalone client/beneficiary links, and claimant-history details. Department users can write current-run workflow-history remarks while authorized and active; recommendation remarks require office access. Office users retain historical record review.

Printable Documents appears after workflow history and its creation form. GIS, certificate, and accessory DOCX downloads require CSWDO or superadmin access. Submitted images require application authorization and a recognized document key; storage filenames are derived server-side.

## Approved rollout procedure

Implementation does not authorize modifying the shared database. Do not run production seeders to upgrade existing accounts.

1. Run `php artisan access:reconcile-roles` for a read-only audit. It lists legacy/canonical role collisions, affected membership counts, staff/CSWDO permission removals, and roles to ensure. It assigns no users.
2. Review the audit and obtain explicit approval. Back up `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, and `role_has_permissions`, using their configured table names. Record the CSWDO and operational assignments that the superadmin will make.
3. Pause access-management writes and prevent users from using the app during the cutover. Deploy the matching code and, only after approval, run `php artisan access:reconcile-roles --apply`.
4. The command renames `lgu admin` to `Admin` and standardizes Budget/Accounting/Treasury names. Same-guard canonical collisions merge memberships and permissions. It preserves direct user permissions and other roles, clears automatic staff/CSWDO permission grants, ensures Releasing/Closer, and clears the permission cache. Team-scoped or inconsistent guard/reference data blocks reconciliation.
5. Explicitly assign CSWDO and any necessary assessor/reporter/Closer/Releasing duties in CMS. No existing user receives these memberships automatically. Verify representative citizen, staff-only, CSWDO, departmental, and superadmin accounts before restoring access.

The command is idempotent. A database backup is the rollback source for original role names, memberships, and permission grants; coordinate any code rollback with restoration of the matching authorization data. Verification here uses isolated SQLite, not a shared-database rollout or MySQL certification.

Focused tests: `php vendor/phpunit/phpunit/phpunit tests/Feature/AccessControlTest.php tests/Feature/DocumentTemplateTest.php tests/Feature/ClaimantReplacementTest.php tests/Feature/WorkflowEditingTest.php tests/Feature/DashboardTest.php`.
