function escapeTableText(value) {
	const div = document.createElement('div');
	div.textContent = String(value ?? '');
	return div.innerHTML;
}

function sameOriginTableUrl(value) {
	if (typeof value !== 'string' || !value.trim() || value.trim() === '#') return null;
	try {
		const url = new URL(value, window.location.origin);
		if (!['http:', 'https:'].includes(url.protocol) || url.origin !== window.location.origin || url.username || url.password) {
			return null;
		}
		return url.href;
	} catch {
		return null;
	}
}

function selectDataTables() {
	let dataTables = $('.data-table');
	if (dataTables.length == 0) return;

	dataTables.each(function() {
		let dataTable = $(this);
		initDataTable(dataTable);
	})
}

function initDataTable(dataTable) {
	if (!dataTable.length || $.fn.DataTable.isDataTable(dataTable[0])) return;
	let columns = (dataTable.data('columns') || []).map(column => ({
		...column,
		// DataTables also uses display output for responsive detail rows.
		render: (value, type) => ['display', 'filter'].includes(type)
			? escapeTableText(value) : (value ?? ''),
	}));
	// DataTables merges the table's data attributes after explicit options.
	dataTable.data('columns', columns);
	let route = dataTable.data('route') ?? '#';
	let dataset = dataTable.data('rows') || [];
	let dataSrc = dataTable.data('src') || null;
	let countPerPage = dataTable.data('count-per-page') || 10;

	if (dataTable.hasClass('with-status')) {
		const statusColumn = columns.find(column => column.data === 'status');

		if (statusColumn) {
			statusColumn.render = (value, type) => {
				const label = String(value && typeof value === 'object' ? (value.label ?? 'pending') : (value ?? 'pending'));
				if (type !== 'display') return type === 'filter' ? escapeTableText(label) : label;
				const colors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];
				const color = colors.includes(value?.badgeColor) ? value.badgeColor : 'primary';
				const badge = document.createElement('span');
				badge.className = `badge badge-${color}`;
				badge.textContent = label.toUpperCase();
				return badge.outerHTML;
			};
		}
	}

	if (dataTable.hasClass('with-actions')) {
		const actionsColumn = columns.find(column => column.data === 'show_route');

		if (actionsColumn) {
			actionsColumn.orderable = false;
			actionsColumn.searchable = false;
			actionsColumn.className = 'text-center';

			actionsColumn.render = (value, type) => {
				if (type !== 'display') return '';
				const url = sameOriginTableUrl(value);
				if (!url) return '';
				const link = document.createElement('a');
				link.href = url;
				link.className = 'btn btn-sm btn-primary hover-scale';
				link.innerHTML = `
					<i class="ki-duotone ki-eye pe-1">
						<span class="path1"></span>
						<span class="path2"></span>
						<span class="path3"></span>
					</i>
					View
				`;
				return link.outerHTML;
			};
		}
	}
	
	dataTable.DataTable({
		responsive: true,
		ordering: true,
		columns: columns,
		order: [],
		pageLength: countPerPage,
		lengthMenu: [...new Set([countPerPage, 10, 25, 50, 100])].sort((a, b) => a - b),
		data: route === '#' || !dataSrc ? dataset : undefined,
		ajax: route !== '#' && dataSrc ? {
			url: route,
			type: 'GET',
			dataSrc: dataSrc,
			headers: {
				'Accept': 'application/json'
			},
			error: function(response) {
				if (response.status === 403) {
					window.location.reload();
				}
			}
		} : undefined,
		dom:
			// First row: buttons on the left, filter on the right
			"<'row mb-2'<'col-sm-6 d-flex align-items-center'l><'col-sm-6 d-flex justify-content-end'f<'me-5'>B>>" +
			// Table
			"<'row'<'col-12'tr>>" +
			// Bottom row: info and pagination
			"<'row mt-2'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",
		buttons: [{
				extend: 'excel',
				text: 'Export to Excel',
				className: '',
			},
			// {
			//     extend: 'print',
			//     text: 'Print',
			//     className: 'btn btn-secondary py-1 px-3 ml-2',
			// },
			// 'copy', 
			// 'csv', 
			// 'pdf',
			// 'print'
		],
		classes: {
			sortAsc: '', // override ascending class
			sortDesc: '', // override descending class
			sortable: '' // override neutral sortable class
		}
	});


	dataTable.off('click.burialTable', 'a').on('click.burialTable', 'a', function(e) {
		e.preventDefault();
		const url = sameOriginTableUrl($(this).attr('href'));
		if (url) {
	
			triggerLoading(0.3);
			setTimeout(() => {
				window.location.href = url;
			}, 300);
		}
	})
}

function refreshDataTable() {
    Livewire.on('refresh-dataTable', (event) => {
        const data = event[0];
        const element = document.getElementById(`dataTable-${data.dataTableId}`);
        if (!element) return;
        const dataTable = $(element);

        if (!$.fn.DataTable.isDataTable(element)) {
			initDataTable(dataTable);
        }
    
        const table = dataTable.DataTable();
    
        // Preserve the current table state.
        const currentPage = table.page();
        const currentSearch = table.search();
        const currentOrder = table.order();
    
        // Replace the rows without reinitializing DataTables.
        table.clear();
        table.rows.add(data.rows);
    
        // Restore search and sorting.
        table.search(currentSearch);
        table.order(currentOrder);
    
        // Redraw without resetting pagination.
        table.draw(false);
    
        // Restore the current page if it remains valid.
        const pageInfo = table.page.info();
    
        if (currentPage < pageInfo.pages) {
            table.page(currentPage).draw('page');
        } else if (pageInfo.pages > 0) {
            table.page(pageInfo.pages - 1).draw('page');
        }
    });
}

export { selectDataTables, initDataTable, refreshDataTable }
