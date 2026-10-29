// This script runs as a same-origin asset inside the disposable fixture only.
if (!window.cspFixture) {
    window.cspFixture = { violations: [], errors: [], trustedScriptRan: true };
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
document.addEventListener('livewire:navigated', renderCspStatus);
document.addEventListener('livewire:init', () => Livewire.hook('morphed', renderCspStatus), { once: true });
