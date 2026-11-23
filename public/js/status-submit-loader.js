let installed = false;

export default function initStatusSubmitLoader() {
    if (installed || !window.Livewire) return;
    installed = true;
    let pending = 0;
    let redirecting = false;

    const hideLoaders = () => {
        window.hideLoading?.(0);
        if (window.Swal?.getPopup()?.dataset.statusSubmitLoading === 'true') {
            window.Swal.close();
        }
    };

    Livewire.hook('commit', ({ component, commit, succeed, respond }) => {
        const actions = (component.el.dataset.statusLoading || '').split(/\s+/).filter(Boolean);
        if (!commit.calls?.some(call => actions.includes(call.method))) return;

        pending++;
        if (component.el.dataset.statusLoadingDialog === 'true' && window.Swal) {
            window.Swal.fire({
                title: 'Please wait',
                text: 'Saving your changes…',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => window.Swal.showLoading(),
            });
            const popup = window.Swal.getPopup();
            if (popup) popup.dataset.statusSubmitLoading = 'true';
        } else {
            window.triggerLoading?.(0.1);
        }
        succeed(({ effects }) => {
            if (effects.redirect) redirecting = true;
        });
        // Livewire runs respond at message completion, including network errors/cancellation.
        respond(() => {
            pending--;
            if (pending === 0 && !redirecting) hideLoaders();
        });
    });

    document.addEventListener('livewire:navigated', () => {
        redirecting = false;
        if (pending === 0) hideLoaders();
    });
}
