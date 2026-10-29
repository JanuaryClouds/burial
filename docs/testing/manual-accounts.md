# Manual testing accounts

These are development/UAT fixtures. They are not an upgrade procedure for existing production users. Seeders have not been run against the shared database as part of this change.

`DatabaseSeeder` creates permissions and roles before users. To seed only the account fixtures on an initialized development database, run PermissionSeeder, RoleSeeder, UserSeeder, and AdminSeeder in that order. RoleSeeder synchronizes permissions for its predefined roles; UserSeeder resets the example.org fixtures below to their listed roles and removes their direct permissions. Rerunning these seeders discards manual access changes made to those fixtures.

All internal test accounts initially use the configured `ADMIN_PASSWORD`. No new hard-coded password is supplied. Existing password hashes are preserved on reruns, including the superadmin's. Accounts must be active to log in through `/admin/login`.

For newly created internal accounts, `ADMIN_PASSWORD` must meet the account policy
(12 characters, uppercase, lowercase, number, symbol, maximum 72 bytes). Their
owners must change it at first login. Reruns do not reset an existing password or
its first-login flag. Citizens continue to use the portal; their UAT credentials
are preserved. See [account management](../architecture/account-management.md).

If seeding reports an invalid `ADMIN_PASSWORD`, set a unique compliant initial
password in your environment. Refresh any cached configuration before retrying
(locally, `php artisan config:clear`; use your normal configuration-cache deployment
step on Hostinger). The seeder reports the unmet rules without printing the secret
and checks it before creating citizen fixtures or contacting the portal.

## Office and access-boundary accounts

All addresses in the following table end in **@example.org**, and every account receives `staff` membership.

| Email prefix | Additional roles / testing purpose |
| --- | --- |
| `staff1` | None: internal landing page, no record browsing, printing, or citizen application |
| `staff2` | CSWDO: office records and printable documents, no office action permissions |
| `staff3` | CSWDO + assessor: interviews, assessments, recommendations, and referrals |
| `staff4` | CSWDO + reporter: reports and exports |
| `staff5` | CSWDO + Releasing + Closer: releasing and permitted closure actions |
| `cswdo-interview` | CSWDO + interviewer: interview actions only |
| `cswdo-assessment` | CSWDO + assessment-officer: assessment actions only |
| `cswdo-recommendation` | CSWDO + recommender: recommendation actions only |
| `cswdo-referral` | CSWDO + referral-officer: referral actions only |
| `cswdo-rejection` | CSWDO + rejection-officer: rejection actions only |
| `cswdo-full` | CSWDO + assessor + reporter + rejection-officer + Releasing + Closer: complete office action coverage without CMS access |

`superadmin@example.org` retains the superadmin and staff roles. It remains responsible for CMS/system setup and support. None of the internal accounts can use citizen creation flows.

## Departmental workflow accounts

Use `workflow-admin`, `workflow-maricar`, `workflow-emma`, `workflow-nikki`, `workflow-bao`, `workflow-budget`, `workflow-accounting`, `workflow-treasury`, or `workflow-releasing`, each followed by **@example.org**.

Each has staff plus its corresponding departmental role, with exactly its corresponding `Workflow …` permission. None has CSWDO membership or document-printing access. The Maricar, Emma, Nikki, and BAO roles now match their named stages rather than inheriting Workflow Admin.

Find an application by its exact tracking number or scanned FUNERAL code. Departmental users can view it only when their permission matches the current stage or a permitted next/return destination of its current workflow. An application must have an appropriate recommendation and meet normal processing prerequisites; a login alone does not bypass these rules. When the workflow uses the seeded default stages, the accounts above provide coverage of every stage.

## Citizen portal UAT credentials

The existing `client1@tlcportal.com` through `client20@tlcportal.com` identities and fixed citizen UUIDs are unchanged in offline seeding mode. Portal mode still fetches `client1_tlc` through `client10_tlc`. Portal-sourced values are used only when creating a missing local user.

Existing client emails, citizen links, names, contact information, password hashes, and roleless status are left untouched on reruns. This seeder does not change credentials in the citizens portal. It refuses to assign internal fixture roles if an example.org fixture email already belongs to a portal-linked citizen.
