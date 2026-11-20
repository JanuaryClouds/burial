const boundImmediateMailForms = new WeakSet();
window.initImmediateMailForms = (root = document) => {
    root.querySelectorAll('form[data-immediate-mail]').forEach((form) => {
        if (boundImmediateMailForms.has(form)) return;
        boundImmediateMailForms.add(form);
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const row = form.closest('[data-invitation-id]');
            if (form.dataset.sending === 'true' || row?.dataset.sending === 'true') return;
            if (form.dataset.uncertain === 'true' && !window.confirm('This email may already have been delivered. Retrying may send a duplicate. Continue?')) return;
            const button = form.querySelector('button[type="submit"]');
            const status = form.querySelector('[data-mail-status]');
            const label = button.textContent;
            const body = new FormData(form);
            const rowButtons = row ? [...row.querySelectorAll('button')].map(button => [button, button.disabled]) : [];
            if (row) row.dataset.sending = 'true';
            rowButtons.forEach(([button]) => { button.disabled = true; });
            let refreshResult;
            form.dataset.sending = 'true';
            if (form.hasAttribute('data-refresh-invitations')) form.dispatchEvent(new CustomEvent('invitations:submitting', { bubbles: true }));
            form.setAttribute('aria-busy', 'true');
            button.disabled = true;
            button.textContent = button.dataset.sendingLabel || 'Sending…';
            status.textContent = '';
            let completed = false;
            try {
                // Invitation forms have an input named "action", which shadows form.action.
                const response = await fetch(form.getAttribute('action') || window.location.href, {
                    method: 'POST', body, credentials: 'same-origin',
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const result = await response.json();
                status.textContent = result.errors ? Object.values(result.errors).flat().join(' ') : (result.message || 'Please try again.');
                if (form.hasAttribute('data-refresh-invitations')) {
                    refreshResult = { ok: response.ok, status: response.status, message: status.textContent };
                } else if (response.ok && result.redirect) {
                    completed = true;
                    window.location.assign(result.redirect);
                } else if (response.ok && result.sent) {
                    completed = true;
                    button.textContent = 'Sent';
                } else if (response.ok && result.retryable === false) {
                    completed = true;
                    button.textContent = 'Cannot retry';
                }
            } catch (_) {
                status.textContent = 'The request could not be completed. Check your connection before trying again.';
                if (form.hasAttribute('data-refresh-invitations')) refreshResult = { ok: false, status: 0, message: status.textContent };
            } finally {
                if (!completed) {
                    button.disabled = false;
                    button.textContent = label;
                }
                rowButtons.forEach(([button, disabled]) => { button.disabled = disabled; });
                if (row) row.dataset.sending = 'false';
                form.dataset.sending = 'false';
                form.removeAttribute('aria-busy');
                if (refreshResult) form.dispatchEvent(new CustomEvent('invitations:response', { bubbles: true, detail: refreshResult }));
            }
        });
    });

};
window.initImmediateMailForms();
