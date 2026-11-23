# Business actors

## Context

The four main actors are citizens, City Social Welfare and Development Office (CSWDO) employees, workflow personnel, and superadmins. Access follows the account's assigned roles, permissions, and record relationships, not a person's job title. Beneficiaries are records, not login accounts.

## Required behavior

1. Citizens may create and view their own client, beneficiary, and application records. Preserve existing account associations and claimant-assignment history when determining access; a citizen is not entitled to another citizen's records merely because they know an identifier.
2. After claimant replacement, former claimants retain read-only access to the citizen-facing application view and documents. This does not grant access to internal assessments, recommendations, or staff-only remarks. Former claimants cannot cancel or modify the application.
3. Beneficiaries are deceased people whose basic and social information is supplied by clients. They do not have user accounts.
4. Citizen accounts have `citizen_uuid` and no Spatie roles. Internal accounts have assigned roles and no `citizen_uuid`. Do not create mixed identities. See [Citizens Portal](citizens-portal.md).
5. CSWDO access covers non-draft client records, beneficiary records, and submitted applications. Specific operations still require their action permissions; office membership is not blanket permission to process every action.
6. Workflow personnel may view applications when their permission matches the current stage, next stage, or an earlier return stage in the current recommendation's pinned workflow version. This viewing scope remains available for paused workflows and terminal applications. They may view related client/beneficiary information within the application, but not standalone record indexes. Processing actions still require the relevant workflow permission and valid application state; viewing permission does not authorize a write.
7. Superadmins may access all records and are the only actors permitted to manage system data such as users, roles, reference data, and workflow definitions. Superadmin access does not bypass processing-state or claimant-cycle restrictions.
8. Internal accounts, including superadmins, cannot register as clients or submit citizen applications. Authorized walk-in intake is a separate staff-assisted operation: an active CSWDO account with `Create Walk-in Intake`, or a superadmin, may encode an accountless applicant. The employee remains the encoder, never the applicant's account owner. Later citizen ownership requires authenticated SSO and the intake's private, single-use claim code; personal-detail matching does not grant ownership.
9. Walk-in intake and account linking are disabled by default and controlled by `system_settings.walk_in_intake_enabled`. Only superadmins can change this through the existing fresh-MFA-protected settings flow. When disabled, all walk-in routes, components, and actions must deny access, including superadmins. Preserve existing application records and their ordinary authorized views and processing prerequisites.

## Implementation references

- [Access-role documentation](../../docs/architecture/access-roles.md), [claimant replacement](../../docs/claimant-replacement.md), [account classification](../../app/Services/Auth/AccountAccess.php), and [application access](../../app/Services/ApplicationAccessService.php).
