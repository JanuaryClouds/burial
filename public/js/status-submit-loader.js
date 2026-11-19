let installed = false;

export default function initStatusSubmitLoader() {
    if (installed || !window.Livewire) return;
    installed = true;
    let pending = 0;
    let redirecting = false;

    Livewire.hook('commit', ({ component, commit, succeed, respond }) => {
        const actions = (component.el.dataset.statusLoading || '').split(/\s+/).filter(Boolean);
        if (!commit.calls?.some(call => actions.includes(call.method))) return;

        pending++;
        window.triggerLoading?.(0.1);
        succeed(({ effects }) => {
            if (effects.redirect) redirecting = true;
        });
        // Livewire runs respond at message completion, including network errors/cancellation.
        respond(() => {
            pending--;
            if (pending === 0 && !redirecting) window.hideLoading?.(0);
        });
    });

    document.addEventListener('livewire:navigated', () => {
        redirecting = false;
        if (pending === 0) window.hideLoading?.(0);
    });
}
