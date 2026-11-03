document.querySelectorAll('form[data-immediate-mail]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (form.dataset.sending === 'true') return;
        if (form.dataset.uncertain === 'true' && !window.confirm('This email may already have been delivered. Retrying may send a duplicate. Continue?')) return;
        const button = form.querySelector('button[type="submit"]');
        const status = form.querySelector('[data-mail-status]');
        const label = button.textContent;
        form.dataset.sending = 'true';
        form.setAttribute('aria-busy', 'true');
        button.disabled = true;
        button.textContent = button.dataset.sendingLabel || 'Sending…';
        status.textContent = '';
        let completed = false;
        try {
            const response = await fetch(form.action, {
                method: 'POST', body: new FormData(form), credentials: 'same-origin',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await response.json();
            status.textContent = result.errors ? Object.values(result.errors).flat().join(' ') : (result.message || 'Please try again.');
            if (response.ok && result.redirect) {
                completed = true;
                window.location.assign(result.redirect);
            } else if (response.ok && result.sent) {
                completed = true;
                button.textContent = 'Sent';
            }
        } catch (_) {
            status.textContent = 'The request could not be completed. Check your connection before trying again.';
        } finally {
            if (!completed) {
                button.disabled = false;
                button.textContent = label;
            }
            form.dataset.sending = 'false';
            form.removeAttribute('aria-busy');
        }
    });
});
