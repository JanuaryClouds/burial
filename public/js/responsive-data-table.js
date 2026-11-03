// The DataTables API is the sole owner of search, order and page state.
const instances = new Map();
const badgeColors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];

function element(tag, className = '', text = null) {
    const node = document.createElement(tag);
    node.className = className;
    if (text !== null) node.textContent = String(text);
    return node;
}

export function mountResponsiveDataTable(api, root, safeUrl) {
    if (instances.has(root)) return;
    const fields = JSON.parse(root.dataset.cardFields || '{}');
    const columns = api.settings()[0].aoColumns;
    const controller = new AbortController();
    const listen = (node, event, callback) => node.addEventListener(event, callback, { signal: controller.signal });
    const toolbar = root.querySelector('[data-table-toolbar]');
    const footer = root.querySelector('[data-table-footer]');
    const cards = root.querySelector('[data-table-cards]');
    toolbar.replaceChildren();
    footer.replaceChildren();
    toolbar.className = 'record-data-table-toolbar';
    footer.className = 'record-data-table-footer';

    const searchLabel = element('label', 'record-data-table-search', 'Search');
    const search = element('input', 'form-control form-control-sm');
    search.type = 'search'; search.placeholder = 'Search records';
    searchLabel.append(search);
    const sortLabel = element('label', 'record-data-table-sort', 'Sort');
    const sort = element('select', 'form-select form-select-sm');
    sort.append(new Option('Original order', ''));
    columns.forEach((column, index) => {
        if (!column.bSortable || column.mData === 'show_route') return;
        const title = column.sTitle || String(column.mData).replaceAll('_', ' ');
        sort.append(new Option(`${title} (ascending)`, `${index}:asc`), new Option(`${title} (descending)`, `${index}:desc`));
    });
    sortLabel.append(sort);
    const lengthLabel = element('label', 'record-data-table-length', 'Per page');
    const length = element('select', 'form-select form-select-sm');
    [...new Set([5, 10, 25, 50, 100, api.page.len()])].sort((a, b) => a - b)
        .forEach(size => length.append(new Option(String(size), String(size))));
    lengthLabel.append(length);
    toolbar.append(searchLabel, sortLabel, lengthLabel);

    const info = element('p', 'record-data-table-info text-muted mb-0');
    info.setAttribute('role', 'status'); info.setAttribute('aria-live', 'polite');
    const navigation = element('nav', 'record-data-table-pagination');
    navigation.setAttribute('aria-label', 'Record pages');
    const actions = element('details', 'record-data-table-actions');
    const summary = element('summary', 'btn btn-sm btn-light', 'Actions');
    const excel = element('button', 'btn btn-sm btn-light-primary', 'Export to Excel');
    excel.type = 'button';
    actions.append(summary, excel);
    footer.append(actions, info, navigation);
    let timer;
    listen(search, 'input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => api.search(search.value).page(0).draw(), 200);
    });
    listen(length, 'change', () => api.page.len(Number(length.value)).page(0).draw());
    listen(sort, 'change', () => api.order(sort.value ? [sort.value.split(':')] : []).page(0).draw());
    listen(excel, 'click', () => { api.button('.buttons-excel').trigger(); actions.open = false; });

    // Cards are outside the table's delegated navigation handler.
    listen(cards, 'click', event => {
        const card = event.target.closest('a.record-data-card');
        if (!card || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        const url = safeUrl(card.href);
        if (!url) return;
        event.preventDefault();
        triggerLoading(0.3);
        setTimeout(() => { window.location.href = url; }, 300);
    });

    listen(navigation, 'click', event => {
        const button = event.target.closest('button[data-page]');
        if (button && !button.disabled) api.page(Number(button.dataset.page)).draw('page');
    });

    function render() {
        const page = api.page.info();
        if (document.activeElement !== search) search.value = api.search();
        length.value = String(api.page.len());
        const order = api.order();
        sort.value = order.length ? `${order[0][0]}:${order[0][1]}` : '';
        const fragment = document.createDocumentFragment();
        api.rows({ page: 'current', search: 'applied', order: 'applied' }).data().toArray().forEach(row => {
            const url = safeUrl(row.show_route);
            const card = element(url ? 'a' : 'article', 'record-data-card');
            if (url) card.href = url;
            [['primary', 'h3', 'fs-3 fw-bold'], ['secondary', 'p', 'fw-semibold'], ['tertiary', 'p', 'text-muted']].forEach(([key, tag, css]) => {
                const value = row[fields[key]];
                if (value !== null && value !== undefined && value !== '') {
                    const label = fields.labels?.[fields[key]];
                    card.append(element(tag, css, label ? `${label}: ${value}` : value));
                }
            });
            const bottom = element('div', 'record-data-card-footer');
            const badgeValue = row[fields.badge];
            if (badgeValue !== null && badgeValue !== undefined && badgeValue !== '') {
                const color = badgeColors.includes(badgeValue?.badgeColor) ? badgeValue.badgeColor : 'secondary';
                bottom.append(element('span', `badge badge-${color}`, String((typeof badgeValue === 'object' ? badgeValue.label : badgeValue) ?? '').toUpperCase()));
            }
            bottom.append(element('span', 'text-muted ms-auto', row[fields.date] ?? ''));
            card.append(bottom); fragment.append(card);
        });
        if (!page.recordsDisplay) fragment.append(element('p', 'record-data-table-empty text-muted', page.recordsTotal ? 'No matching records' : 'No records'));
        cards.replaceChildren(fragment);
        info.textContent = page.recordsDisplay
            ? `Showing ${page.start + 1}–${page.end} of ${page.recordsDisplay} records${page.recordsDisplay !== page.recordsTotal ? ` (filtered from ${page.recordsTotal})` : ''}`
            : (page.recordsTotal ? `No matching records (${page.recordsTotal} total)` : 'No records');
        excel.disabled = page.recordsDisplay === 0;
        const oldFocus = navigation.contains(document.activeElement) ? document.activeElement.dataset.page : null;
        navigation.replaceChildren();
        const addPage = (text, target, disabled, current = false) => {
            const button = element('button', `btn btn-sm ${current ? 'btn-primary' : 'btn-light'}`, text);
            button.setAttribute('aria-label', ({ '\u00ab': 'First page', '\u2039': 'Previous page', '\u203a': 'Next page', '\u00bb': 'Last page' })[text] || `Page ${text}`);
            button.type = 'button'; button.disabled = disabled; button.dataset.page = String(target);
            if (current) button.setAttribute('aria-current', 'page');
            navigation.append(button);
        };
        addPage('«', 0, page.page === 0 || !page.pages);
        addPage('‹', Math.max(0, page.page - 1), page.page === 0 || !page.pages);
        const start = Math.max(0, Math.min(page.page - 1, page.pages - 3));
        for (let i = start; i < Math.min(page.pages, start + 3); i++) addPage(String(i + 1), i, false, i === page.page);
        addPage('›', Math.min(page.pages - 1, page.page + 1), !page.pages || page.page >= page.pages - 1);
        addPage('»', Math.max(0, page.pages - 1), !page.pages || page.page >= page.pages - 1);
        if (oldFocus !== null) navigation.querySelector(`[data-page="${oldFocus}"]:not(:disabled)`)?.focus();
    }
    const media = window.matchMedia('(min-width: 992px)');
    const resize = () => { if (media.matches) api.columns.adjust(); };
    listen(media, 'change', resize);
    api.on('draw.responsiveCards', render);
    instances.set(root, () => {
        clearTimeout(timer); controller.abort(); api.off('.responsiveCards');
        api.destroy(); instances.delete(root);
    });
    render(); resize();
}

// A removed/deferred component must not leave an API or media listener behind.
new MutationObserver(() => {
    instances.forEach((dispose, root) => { if (!root.isConnected) dispose(); });
}).observe(document.documentElement, { childList: true, subtree: true });
document.addEventListener('livewire:navigating', () => instances.forEach(dispose => dispose()));
