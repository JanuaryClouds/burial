let initialized = false;

export default function initSuperadminAccess() {
    if (initialized) return;
    initialized = true;

    document.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-superadmin-grant]');
        if (!button || button.disabled) return;
        const component = button.closest('[wire\\:id]');
        const wire = component && Livewire.find(component.getAttribute('wire:id'));
        if (!wire) return;
        event.preventDefault();
        const grant = button.dataset.superadminGrant === 'true';
        button.disabled = true;
        try {
            const result = await Swal.fire({
                title: grant ? 'Grant superadmin access?' : 'Remove superadmin access?',
                text: grant ? 'Grant this account full superadmin access?' : 'Remove superadmin access from this account?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: grant ? 'Grant access' : 'Remove access',
                cancelButtonText: 'Cancel',
                focusCancel: true,
            });
            if (result.isConfirmed) await wire.$call('changeSuperadmin', grant);
        } finally {
            button.disabled = false;
        }
    });
}
