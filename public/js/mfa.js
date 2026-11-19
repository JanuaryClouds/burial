document.addEventListener('DOMContentLoaded', () => {
    const field = document.getElementById('mfa-recovery-codes');
    const status = document.querySelector('[data-mfa-status]');
    document.querySelector('[data-mfa-copy]')?.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(field.value);
            status.textContent = 'Recovery codes copied.';
        } catch {
            field.select();
            status.textContent = 'Select and copy the codes using your browser.';
        }
    });
    document.querySelector('[data-mfa-download]')?.addEventListener('click', () => {
        const url = URL.createObjectURL(new Blob([field.value + '\n'], { type: 'text/plain' }));
        const link = document.createElement('a');
        link.href = url;
        link.download = 'burial-recovery-codes.txt';
        link.click();
        setTimeout(() => URL.revokeObjectURL(url), 1000);
    });
});
