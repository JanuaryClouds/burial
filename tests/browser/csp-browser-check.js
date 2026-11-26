// This script runs as a same-origin asset inside the disposable fixture only.
if (!window.cspFixture) {
    window.cspFixture = { violations: [], errors: [], trustedScriptRan: true, navigations: 0 };
    const openPopup = window.open;
    window.open = function (...args) {
        window.cspFixture.popupUrl = args[0];
        renderCspStatus();
        return openPopup.apply(this, args);
    };
    document.addEventListener('securitypolicyviolation', (event) => {
        window.cspFixture.violations.push({ directive: event.effectiveDirective, blocked: event.blockedURI });
        renderCspStatus();
    });
    window.addEventListener('error', (event) => {
        window.cspFixture.errors.push(event.message);
        renderCspStatus();
    });
    const script = document.createElement('script');
    script.textContent = 'window.cspUntrustedScriptRan = true';
    document.head.appendChild(script);
}
function renderCspStatus() {
    const status = document.getElementById('csp-status');
    if (status) status.textContent = JSON.stringify({ ...window.cspFixture, untrustedScriptBlocked: !window.cspUntrustedScriptRan });
}
renderCspStatus();
document.addEventListener('livewire:navigated', () => {
    window.cspFixture.navigations++;
    renderCspStatus();
    requestAnimationFrame(() => {
        let status = document.getElementById('navigation-fixture-status');
        if (!status) {
            status = document.createElement('output');
            status.id = 'navigation-fixture-status';
            status.setAttribute('aria-label', 'Navigation fixture status');
            document.body.append(status);
        }
        status.textContent = JSON.stringify({
            navigations: window.cspFixture.navigations,
            errors: window.cspFixture.errors,
            unexpectedViolations: window.cspFixture.violations.filter(item => item.blocked !== 'inline'),
            charts: Object.values(window.Chart?.instances || {}).length,
            trackedCharts: window.renderedCharts?.length || 0,
            canvases: document.querySelectorAll('canvas[data-chart-type]').length,
            tables: document.querySelectorAll('.data-table').length,
            toolbars: document.querySelectorAll('[data-table-toolbar] input[type=search]').length,
            loaderHidden: document.getElementById('splashScreen')?.getAttribute('aria-hidden'),
        });
    });
});
document.addEventListener('livewire:init', () => Livewire.hook('morphed', renderCspStatus), { once: true });
