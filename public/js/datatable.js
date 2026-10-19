function selectDataTables() {
	let dataTables = $('.data-table');
	if (dataTables.length == 0) return;

	dataTables.each(function() {
		let dataTable = $(this);
		initDataTable(dataTable);
	})
}

function initDataTable(dataTable) {
	let columns = dataTable.data('columns') || [];
	let route = dataTable.data('route') ?? '#';
	let dataset = dataTable.data('rows') || [];
	let dataSrc = dataTable.data('src') || null;
	let countPerPage = dataTable.data('count-per-page') || 10;

	const escapeHtml = (str) => {
		const div = document.createElement('div');
		div.textContent = str;
		return div.innerHTML;
	}

	if (dataTable.hasClass('with-status')) {
		const statusColumn = columns.find(column => column.data === 'status');

		if (statusColumn) {
			statusColumn.render = (_, __, row) => {
				const rawStatus = row.status.label || 'pending';
				const badgeClass = row.status.badgeColor || 'primary';
				const status = escapeHtml(rawStatus);
				return `
					<span class="badge badge-${badgeClass}">
						${status.toUpperCase()}
					</span>
				`;
			}
		}
	}

	if (dataTable.hasClass('with-actions')) {
		const actionsColumn = columns.find(column => column.data === 'show_route');

		if (actionsColumn) {
			actionsColumn.orderable = false;
			actionsColumn.searchable = false;
			actionsColumn.className = 'text-center';

			actionsColumn.render = (_, __, row) => {
				const routeValue = row.show_route || '#';
				const isValidPath = routeValue.startsWith('/') && !routeValue.startsWith('//');
				const isSameOrigin = routeValue.startsWith(window.location.origin);
				if (!isValidPath && !isSameOrigin) {
					console.warn(`Invalid route: ${routeValue}`);
					return '';
				}
				const safeRoute = encodeURI(routeValue);
				if (safeRoute === '#' || safeRoute === '' || safeRoute === undefined ||
					safeRoute === null) return '';
				return ` 
						<a href="${safeRoute}" class="btn btn-sm btn-primary hover-scale">
							<i class="ki-duotone ki-eye pe-1">
								<span class="path1"></span>
								<span class="path2"></span>
								<span class="path3"></span>
							</i>
							View
						</a>
					`;
				}
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


	$('.data-table').on('click', 'a', function(e) {
		e.preventDefault();
		const url = $(this).attr('href');
		if (url && url !== '#') {
			const isValidPath = url.startsWith('/') && !url.startsWith('//');
			const isSameOrigin = url.startsWith(window.location.origin);
			if (!isValidPath && !isSameOrigin) {
				return;
			}
	
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
        const selector = `#dataTable-${data.dataTableId}`;

        if (!$.fn.DataTable.isDataTable(selector)) {
            // console.warn(`DataTable not initialized: ${selector}`);
            // return;

			initDataTable(data.dataTableId);
        }
    
        const table = $(selector).DataTable();
    
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