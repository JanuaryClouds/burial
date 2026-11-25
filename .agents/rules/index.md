# Project rule index

Read rules by both affected paths and task concerns. These mappings are reading guidance, not access-control configuration or automatically enforced globs. Cross-cutting work often requires more than one file.

Required behavior expresses project intent. Current implementation notes and linked documentation provide evidence, not automatic approval of every existing behavior. Do not silently change application behavior to make it match these rules; application changes require an authorized implementation task.

| Paths or concerns | Read |
| --- | --- |
| All implementation, configuration, dependency, and deployment changes | [Hostinger restrictions](hostinger-restrictions.md) |
| Accounts, roles, policies, ownership, application/client/beneficiary access; `app/Models/**`, `app/Policies/**`, `app/Services/Auth/**`, `app/Services/ApplicationAccessService.php`, related controllers and Livewire components | [Business actors](business-actors.md) |
| Citizen login, SSO, Portal identity and synchronization; `app/Http/Controllers/CitizenAccessController.php`, `app/Services/CentralClientService.php`, `config/services.php`, authentication routes | [Citizens Portal](citizens-portal.md) and business actors |
| Uploads, retrieval, image display, fileserver settings and mock; `app/Services/Image*.php`, `app/Services/FileserverPolicy.php`, `app/Services/ApplicationSubmissionService.php`, `app/Http/Controllers/ImageController.php`, `app/Support/MockFileserverStorage.php`, document submission flows | [Fileserver](fileserver.md) and business actors |
| Mail, invitations, recovery, suppression logs, retry; `app/Services/Mail/**`, `app/Mail/**`, mail controllers/commands, `config/mail.php`, related database connections | [Mail](mail.md) |
| SMS and interview notifications; `app/Services/InterviewSchedulingService.php`, `app/Services/SmsService.php`, `app/Services/NotificationService.php`, `app/Http/Controllers/InterviewController.php`, `config/services.php` | [SMS](sms.md); also workflow transitions when changing scheduling |
| Processing, claimant replacement, recommendations, payments, versioned workflows and custom fields; relevant services, policies, models, migrations and Livewire components | [Workflow transitions](workflow-transitions.md) and business actors |
| Tests and verification; `tests/**`, `phpunit.xml`, `.env.testing`, `phpstan.neon`, CI and database setup | [Testing](testing.md), plus rules for the behavior under test |

For `config/services.php`, read the rules for the integration being changed. For rule edits, read this index and the affected rule files. Keep links and mappings current without expanding unrelated rules.
