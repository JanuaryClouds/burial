import { initDataTable } from './datatable.js';

export function mountInvitationsLive(root) {
    const element = root.querySelector('.data-table');
    initDataTable($(element));
    const table = $(element).DataTable();
    const notice = document.querySelector('[data-invitation-refresh-status]');
    const actionNotice = document.querySelector('[data-invitation-action-status]');
    let inFlight = false, refreshAgain = false, generation = 0, controller;
    const nodes = () => table.rows().nodes().toArray();
    const busy = () => nodes().some(row => row.dataset.sending === 'true');

    const bind = () => {
        window.initImmediateMailForms?.(element);
        element.querySelectorAll('select[data-control="select2"]').forEach(select => {
            if (!$(select).data('select2')) window.initSelect2?.(select.parentElement);
        });
    };
    const setStale = stale => {
        nodes().forEach(row => row.querySelectorAll('button, select').forEach(control => {
            if (stale && !control.disabled) {
                control.dataset.staleDisabled = 'true';
                control.disabled = true;
            } else if (!stale && control.dataset.staleDisabled === 'true') {
                delete control.dataset.staleDisabled;
                control.disabled = false;
            }
        }));
    };
    const remove = row => {
        row.querySelectorAll('select').forEach(select => {
            if ($(select).data('select2')) $(select).select2('destroy');
        });
        table.row(row).remove();
    };
    const apply = html => {
        const body = document.createElement('tbody');
        // Only same-origin, authorized Blade output is used for interactive cells.
        body.innerHTML = html;
        const incoming = new Map([...body.children].map(row => [row.dataset.invitationId, row]));
        let changed = false;
        nodes().forEach(row => {
            const next = incoming.get(row.dataset.invitationId);
            incoming.delete(row.dataset.invitationId);
            if (next?.dataset.invitationVersion === row.dataset.invitationVersion) return;
            // Preserve unsaved role selections while pending, but retire completed rows immediately.
            if (next?.dataset.invitationPending === 'true' && row.dataset.rolesDirty === 'true') return;
            remove(row);
            if (next) table.row.add(next);
            changed = true;
        });
        incoming.forEach(row => { table.row.add(row); changed = true; });
        if (changed) {
            const page = table.page();
            table.draw(false);
            const lastPage = Math.max(0, table.page.info().pages - 1);
            table.page(Math.min(page, lastPage)).draw('page');
        }
        bind();
    };
    async function refresh(force = false) {
        if (!root.isConnected || busy() || (document.hidden && !force)) return;
        if (inFlight) { refreshAgain ||= force; return; }
        inFlight = true;
        const current = generation;
        controller = new AbortController();
        try {
            const response = await fetch(root.dataset.refreshUrl, {
                headers: { Accept: 'application/json' }, credentials: 'same-origin', cache: 'no-store',
                redirect: 'error', signal: controller.signal,
            });
            if (!response.ok) throw new Error('Refresh failed');
            const data = await response.json();
            if (typeof data.html !== 'string') throw new Error('Invalid refresh');
            if (current !== generation || busy()) return;
            setStale(false);
            apply(data.html);
            if (notice) notice.textContent = 'Up to date. Invitations refresh automatically every 10 seconds.';
        } catch (error) {
            if (error.name === 'AbortError' || current !== generation) return;
            setStale(true);
            if (notice) notice.textContent = 'Unable to refresh invitations. Actions are paused until the connection is restored.';
        } finally {
            inFlight = false;
            if (refreshAgain) { refreshAgain = false; refresh(true); }
        }
    }
    $(element).on('draw.dt.invitations', bind);
    $(element).on('change.invitations', 'select[name="roles[]"]', function () {
        this.closest('[data-invitation-id]').dataset.rolesDirty = 'true';
    });
    document.addEventListener('invitations:submitting', () => {
        generation++;
        controller?.abort();
    });
    document.addEventListener('invitations:response', event => {
        generation++;
        const form = event.target;
        const row = form.closest('[data-invitation-id]');
        if (actionNotice) actionNotice.textContent = event.detail.message;
        if (event.detail.ok) {
            if (row) { delete row.dataset.rolesDirty; row.dataset.invitationVersion = ''; }
            else {
                form.reset();
                $(form).find('select').val([]).trigger('change');
            }
        }
        refresh(true);
    });
    window.addEventListener('focus', () => refresh(true));
    document.addEventListener('visibilitychange', () => { if (!document.hidden) refresh(true); });
    const interval = setInterval(() => {
        if (!root.isConnected) { clearInterval(interval); controller?.abort(); return; }
        refresh();
    }, 10000);
    bind();
    refresh(true);
    return { refresh };
}

const initialize = () => {
    const root = document.querySelector('[data-invitations-live]');
    if (root) mountInvitationsLive(root);
};
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialize, { once: true });
else initialize();
