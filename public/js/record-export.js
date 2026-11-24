document.querySelectorAll('form[data-record-export]').forEach(form => {
    const button = form.querySelector('button[type="submit"]');
    const feedback = form.querySelector('[data-export-error]');
    const failureMessage = 'The Excel download could not start. Please try again.';
    const modalElement = form.closest('.modal');

    const hideFilters = async modal => {
        if (!modal || !modalElement.classList.contains('show')) return;

        await new Promise(resolve => {
            const hide = () => modal.hide();
            // Bootstrap ignores hide() while the opening transition is still running.
            modalElement.addEventListener('shown.bs.modal', hide, { once: true });
            modalElement.addEventListener('hidden.bs.modal', () => {
                modalElement.removeEventListener('shown.bs.modal', hide);
                resolve();
            }, { once: true });
            hide();
        });
    };

    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (button.disabled) return;

        const url = new URL(form.action);
        url.search = new URLSearchParams(new FormData(form)).toString();
        button.disabled = true;
        form.setAttribute('aria-busy', 'true');
        feedback.hidden = true;
        const swal = window.Swal;
        const modal = window.bootstrap?.Modal.getInstance(modalElement);

        let message = '';
        let loadingPopup;
        try {
            if (swal) {
                await hideFilters(modal);
                swal.fire({
                    title: 'Preparing Excel download',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => swal.showLoading(),
                });
                loadingPopup = swal.getPopup();
            }

            const response = await fetch(url, {
                headers: { Accept: 'application/json' },
                redirect: 'manual',
            });
            if (!response.ok) {
                if ([404, 422].includes(response.status) && response.headers.get('Content-Type')?.includes('application/json')) {
                    const result = await response.json();
                    message = result.message || failureMessage;
                } else {
                    message = response.status === 429 ? 'Please wait before trying another Excel export.' : failureMessage;
                }
            } else {
                const disposition = response.headers.get('Content-Disposition') || '';
                if (!/^attachment\b/i.test(disposition)) throw new Error(failureMessage);

                const file = await response.blob();
                const fileUrl = URL.createObjectURL(file);
                const link = document.createElement('a');
                link.href = fileUrl;
                link.download = disposition.match(/filename="?([^";]+)/i)?.[1] || 'export.xlsx';
                document.body.appendChild(link);
                try {
                    link.click();
                } finally {
                    link.remove();
                    setTimeout(() => URL.revokeObjectURL(fileUrl), 60000);
                }
                if (!swal) await hideFilters(modal);
            }
        } catch {
            message = failureMessage;
        } finally {
            button.disabled = false;
            form.removeAttribute('aria-busy');
            if (loadingPopup && swal.getPopup() === loadingPopup) swal.close();
        }

        if (message) {
            if (swal) {
                await swal.fire({ icon: 'warning', text: message, returnFocus: false });
                modal?.show();
            } else {
                feedback.textContent = message;
                feedback.hidden = false;
            }
        }
    });
});
