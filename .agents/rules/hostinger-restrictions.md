# Shared Hostinger restrictions

## Context

Multiple LGU projects share a Hostinger account and use subdomains. Storage, inode limits, runtime compatibility, and restrictions on continuously running processes constrain this project's design. These are project operating constraints, not a claim about every Hostinger plan.

## Required behavior

1. Target the team's shared PHP runtime. The current documented target is PHP 8.4.27, with Composer's platform set accordingly. Verify installed package compatibility before changing runtime or dependencies; local success on a newer PHP version is insufficient.
2. Use the existing public assets and Blade/Alpine frontend. Node/npm setup, development commands, and frontend builds are outside this project's supported workflow, even though package/Vite configuration remains in the repository.
3. Do not introduce queue-worker, continuously running service, or browser-automation dependencies on the shared host. Application email and SMS use request-driven services. An asynchronous browser request is not a server-side background worker.
4. Store submitted application images through the external fileserver, not as permanent Hostinger uploads. Preserve application storage needed for sessions, caches, logs, and temporary operations; the image restriction is not a blanket prohibition on all filesystem use.
5. Keep secrets in environment configuration and preserve existing application and fileserver encryption keys. Use locked Composer dependencies and check actual host platform requirements.
6. Configuration, optimization, schema, and deployment commands affecting the shared host follow the team's reviewed deployment process. A local code/rule change does not authorize production operations or changes to other projects.

## Environment boundaries and references

- Local/test helper processes, isolated fileserver mocks, Mailpit, and disposable concurrency-test processes do not authorize deploying continuous services on Hostinger.
- Reported inode growth had no confirmed cause as of November 20, 2026. Do not present Composer or Node modules as the established cause or remove dependencies to address that suspicion.
- [README](../../README.md), [PHP compatibility](../../docs/php-8.4-compatibility.md), and [deployment environment](../../docs/deployment-environment.md) describe setup and host checks.
