# Content Security Policy

The application defaults to `CSP_MODE=report-only` in every environment. This
sends `Content-Security-Policy-Report-Only`: the browser diagnoses violations
without blocking resources. Production does not automatically enable enforcement.

| Mode | Document response |
| --- | --- |
| `report-only` (default) | Report-only policy |
| `enforce` | Enforced `Content-Security-Policy` |
| `off` | No application CSP header |

An unknown mode falls back to report-only. Configuration is in `config/csp.php`.
If configuration is cached, rebuild the deployment's configuration cache after
changing the environment setting. For rollback, select report-only (or off) and
refresh the page: a response cannot remove a policy from an already loaded document.
Keep Livewire's `csp_safe=true`; disabling the header does not change its evaluator.

## Script and resource rules

`SecurityHeaders` generates a 32-byte random nonce for each document response,
shares it with Blade, and adds it to the script policy. Use
`nonce="{{ $nonce ?? '' }}"` only on explicitly trusted script tags. Do not
stamp nonces onto arbitrary user HTML. HTML with an active policy is private and
not stored in shared caches. JSON updates and document downloads do not receive
an HTML policy. There is no public reporting endpoint; use browser developer tools.

The middleware also publishes the document nonce through the `csp-nonce` container
binding detected by the installed Laravel Debugbar. This covers its trusted
injected scripts when local debugging is enabled; no Vite build or broad HTML
nonce replacement is involved. Keep debugging disabled in production. An inline
violation's suggested hash is diagnostic evidence, not a reason to add that hash
or `unsafe-inline` without first identifying the script and its source location.

The script policy excludes `unsafe-inline` and `unsafe-eval`, and inline event
attributes are blocked. Use external modules or nonced script blocks, and
`addEventListener` for DOM events. The image viewer uses a delegated listener so
it also works after a Livewire morph. The landing-page script is a static asset.

Livewire serves its existing CSP-safe build without an asset build step. Its
installed runtime preserves the document nonce during navigation and dynamic
script loading; do not reuse a session nonce or implement blanket nonce rewriting.
The workflow password visibility expressions use the supported Alpine CSP syntax.

Existing required CDN origins are explicitly listed. Inline styles remain allowed
because Bootstrap, Metronic, Select2 and charts use them. Images allow self, data
and blob sources; server-side fileserver calls are not browser `connect-src`
requests. Do not add a remote service to that directive unless the browser really
contacts it. This work does not change SSO, file uploading or unrelated headers.

## Isolated browser checks

The fixture uses real application layouts, Livewire, Alpine, Select2, notifications
and the image viewer. It serves only on loopback with a database and sessions in a
new OS temporary directory. Its router refuses ordinary web/PHP execution and
never exposes fixture routes through the application's normal route files.
It forces enforcement, blocks outbound Laravel HTTP requests, and disables
integration writes. Never point it at a real database.

From the repository root, in a dedicated PowerShell terminal:

```powershell
$cspDirectory = Join-Path $env:TEMP ('burial-csp-' + [guid]::NewGuid())
New-Item -ItemType Directory -Path $cspDirectory | Out-Null
New-Item -ItemType File -Path (Join-Path $cspDirectory 'fixture.sqlite') | Out-Null
$env:CSP_BROWSER_DIRECTORY = $cspDirectory
$env:APP_ENV = 'testing'
$env:APP_CONFIG_CACHE = Join-Path $cspDirectory 'absent-config.php'
$env:DB_CONNECTION = 'sqlite'
$env:DB_DATABASE = Join-Path $cspDirectory 'fixture.sqlite'
$env:DATABASE_URL = ''
php -S 127.0.0.1:8765 -t public tests/browser/csp-router.php
```

Open `/__csp/login` to enter the synthetic staff account and dashboard, then
`/__csp/probe`. Check increment/toast, repeated polling, password visibility,
Select2, image popup, and Livewire navigation. The status reports exactly one
intentional inline-script violation and `untrustedScriptBlocked=true`; application
errors or additional violations need investigation. The fixture password field
is synthetic and does not change credentials. Use `/__csp/guest` for the landing
page and `/admin/login` for the login page. `/__csp/tables` runs the existing
29 DataTables security checks under the same enforced policy. The document link
downloads a synthetic attachment; real document generation is covered by the
isolated `DocumentTemplateTest` suite. Stop the server when finished.

Feature tests are in `SecurityHeadersTest`. Run against an isolated testing
database with configuration-cache bypass and outbound integration writes disabled,
as with the other security regression suites. They cover modes, document nonce
consistency/uniqueness, script restrictions, guest layouts and non-HTML responses.

## Enforcement gate and Hostinger checks

Before selecting `enforce` on Hostinger:

- Inspect effective response headers for conflicting hosting/proxy policies.
- Check guest and authenticated pages, password changes, menus/theme, alerts,
  Select2, tables, charts, workflow processing, image views and printable links.
- Repeat after polls, deferred component loads and `wire:navigate` transitions.
- Inspect console/network failures; distinguish intentional fixture violations,
  browser extensions and unavailable CDN resources from application violations.
- Verify portal navigation is a full-page handoff: `frame-ancestors 'none'` forbids
  embedding Burial in an iframe. Confirm all actual form targets and assets.
- Validate with representative production data and browsers; local fixture success
  is not evidence of Hostinger or fileserver compatibility.

Report-only mode remains the deployment default until those checks are complete.

## Local verification (2026-10-28)

- Selected security, account, claimant, workflow, template and CSP suites: 149
  tests, 803 assertions, no failures; one MySQL-specific test skipped on SQLite.
- All 29 existing DataTables browser checks passed under enforcement.
- Enforced-browser checks covered landing/login (including visibility control),
  dashboard charts, workflow editor field/stage actions, theme menu/switching,
  deferred loading, polling, Livewire updates/navigation, Select2 and toasts.
- The probe recorded only the intentional inline-script rejection, no runtime
  errors, and successful same-origin attachment download. The image button
  dispatched the expected popup URL; actual popup-window behavior depends on the
  browser's popup settings and remains part of the deployment checklist.
- Real document generation/access was tested in the template feature suite;
  browser downloads used a synthetic attachment. Hostinger, live portal handoff,
  fileserver uploads and representative production data were not exercised.
