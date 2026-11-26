// Metronic's global delegates must outlive the body replaced by wire:navigate.
// Instance delegates (for example a particular drawer) remain on their own body.
for (const [owner, method] of [
    [window.KTApp, 'init'], [window.KTMenu, 'initHandlers'],
    [window.KTDrawer, 'handleShow'], [window.KTDrawer, 'handleDismiss'],
]) {
    if (!owner?.[method]) continue;
    const original = owner[method];
    owner[method] = function (...args) {
        const delegate = KTUtil.on;
        KTUtil.on = (element, selector, event, handler) => {
            if (element === document.body && owner === window.KTMenu && event === 'click') {
                const menuHandler = handler;
                handler = function (event) {
                    // Sidebar labels are hover menus on desktop, but on mobile
                    // their click handler must yield to Livewire's link handler.
                    if (this.matches('a[wire\\:navigate]')) return;
                    return menuHandler.call(this, event);
                };
            }
            return delegate(element === document.body ? document : element, selector, event, handler);
        };
        try { return original.apply(this, args); }
        finally { KTUtil.on = delegate; }
    };
}

let previousBody = document.body;

document.addEventListener('click', event => {
    const trigger = event.target.closest('[data-kt-menu-trigger]');
    if (trigger) window.bootstrap?.Tooltip.getInstance(trigger)?.hide();
});

export function initNavigationWidgets() {
    if (previousBody === document.body) return;
    previousBody = document.body;
    window.KTComponents?.init();
    window.KTThemeMode?.init();
}

export function disposeNavigationWidgets() {
    window.Swal?.close();
    window.toastr?.remove();
    for (const [name, selector] of [
        ['Modal', '.modal'], ['Offcanvas', '.offcanvas'], ['Tooltip', '[data-bs-toggle="tooltip"]'],
        ['Popover', '[data-bs-toggle="popover"]'], ['Dropdown', '[data-bs-toggle="dropdown"]'],
    ]) {
        document.querySelectorAll(selector).forEach(element => window.bootstrap?.[name]?.getInstance(element)?.dispose());
    }
    document.querySelectorAll('select').forEach(element => {
        if ($(element).data('select2')) $(element).select2('destroy');
    });
    for (const [name, selector] of [
        ['KTDrawer', '[data-kt-drawer="true"]'], ['KTMenu', '[data-kt-menu="true"]'],
        ['KTScroll', '[data-kt-scroll="true"]'], ['KTSticky', '[data-kt-sticky="true"]'],
        ['KTSwapper', '[data-kt-swapper="true"]'], ['KTToggle', '[data-kt-toggle]'],
        ['KTScrolltop', '[data-kt-scrolltop="true"]'], ['KTDialer', '[data-kt-dialer="true"]'],
        ['KTImageInput', '[data-kt-image-input]'], ['KTPasswordMeter', '[data-kt-password-meter]'],
    ]) {
        document.querySelectorAll(selector).forEach(element => {
            const instance = window[name]?.getInstance(element);
            if (name === 'KTDrawer') instance?.hide();
            instance?.destroy?.();
        });
    }
    document.querySelectorAll('.data-table').forEach(element => {
        if ($.fn.DataTable.isDataTable(element)) $(element).DataTable().destroy();
    });
    document.querySelectorAll('[data-kt-initialized]').forEach(element => element.removeAttribute('data-kt-initialized'));
    document.querySelectorAll('.modal-backdrop, .offcanvas-backdrop, .drawer-overlay, .tooltip, .popover').forEach(element => element.remove());
}
