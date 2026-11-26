let hideTimer;
let navigationTimer;

window.triggerLoading = function (duration = 1) {
    clearTimeout(hideTimer);
    const splash = document.getElementById('splashScreen');
    if (!splash) return;
    splash.setAttribute('aria-hidden', 'false');
    Object.assign(splash.style, { zIndex: '9999', opacity: '1', transition: `opacity ${duration}s ease` });
};

window.hideLoading = function (duration = 1) {
    clearTimeout(hideTimer);
    const splash = document.getElementById('splashScreen');
    if (!splash) return;
    splash.setAttribute('aria-hidden', 'true');
    splash.style.opacity = '0';
    splash.style.transition = `opacity ${duration}s ease`;
    hideTimer = setTimeout(() => { splash.style.zIndex = '-1'; }, duration * 1000);
};

document.addEventListener('submit', event => {
    const form = event.target;
    if (event.defaultPrevented || form.matches('[data-no-loader]') ||
        form.getAttributeNames().some(name => name === 'wire:submit' || name.startsWith('wire:submit.'))) return;
    event.preventDefault();
    window.triggerLoading(0.3);
    navigationTimer = setTimeout(() => form.submit(), 300);
});

document.addEventListener('click', event => {
    const link = event.target.closest('a[href]');
    if (!link || event.defaultPrevented || event.button !== 0 ||
        event.ctrlKey || event.metaKey || event.shiftKey || event.altKey ||
        link.matches('[wire\\:navigate], [download], [data-no-loader], [data-bs-dismiss]') ||
        (link.target && link.target !== '_self')) return;
    const href = link.getAttribute('href');
    if (!href || href.startsWith('#')) return;
    const url = new URL(href, location.href);
    if (!['http:', 'https:'].includes(url.protocol) || url.origin !== location.origin) return;
    event.preventDefault();
    window.triggerLoading(0.3);
    navigationTimer = setTimeout(() => { window.location.href = url.href; }, 300);
});

document.addEventListener('livewire:navigating', () => {
    clearTimeout(navigationTimer);
    clearTimeout(hideTimer);
});
document.addEventListener('livewire:navigated', () => window.hideLoading(0));
window.addEventListener('pageshow', () => window.hideLoading(0));
