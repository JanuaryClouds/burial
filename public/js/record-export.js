const exports = new Map();

export function initRecordExports() {
    document.querySelectorAll('form[data-record-export]').forEach(form => {
        if (exports.has(form)) return;
        const controller = new AbortController();
        exports.set(form, controller);
        const button = form.querySelector('button[type="submit"]');
        const feedback = form.querySelector('[data-export-error]');
        const failureMessage = 'The Excel download could not start. Please try again.';
        const modalElement = form.closest('.modal');

        const hideFilters = async modal => {
            if (!modal || !modalElement.classList.contains('show')) return;

            await new Promise(resolve => {
                const hide = () => modal.hide();
                // Bootstrap ignores hide() while the opening transition is still running.
                modalElement.addEventListener('shown.bs.modal', hide, { once: true, signal: controller.signal });
                controller.signal.addEventListener('abort', resolve, { once: true });
                modalElement.addEventListener('hidden.bs.modal', () => {
                    modalElement.removeEventListener('shown.bs.modal', hide);
                    controller.signal.removeEventListener('abort', resolve);
                    resolve();
                }, { once: true, signal: controller.signal });
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
                    if (controller.signal.aborted) return;
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
                    signal: controller.signal,
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
            } catch (error) {
                if (error.name !== 'AbortError') message = failureMessage;
            } finally {
                button.disabled = false;
                form.removeAttribute('aria-busy');
                if (loadingPopup && swal.getPopup() === loadingPopup) swal.close();
            }

            if (message && form.isConnected && !controller.signal.aborted) {
                if (swal) {
                    await swal.fire({ icon: 'warning', text: message, returnFocus: false });
                    modal?.show();
                } else {
                    feedback.textContent = message;
                    feedback.hidden = false;
                }
            }
        }, { signal: controller.signal });
    });
}

initRecordExports();
document.addEventListener('livewire:navigated', initRecordExports);
document.addEventListener('livewire:navigating', () => {
    exports.forEach(controller => controller.abort());
    exports.clear();
});
