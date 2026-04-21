<script>
    document.addEventListener('DOMContentLoaded', function() {
        let datatable = $('.data-table');
        if (datatable.length == 0) return;

        let columns = datatable.data('columns') || [];
        let route = datatable.data('route') ?? null;

        const escapeHtml = (str) => {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        if (datatable.hasClass('with-status')) {
            columns.push({
                data: null,
                title: 'Status',
                orderable: true,
                searchable: true,
                className: 'text-center',
                render: (_, __, row) => {
                    const rawStatus = row.status || 'pending';
                    const status = escapeHtml(rawStatus);
                    let badgeClass;
                    switch (rawStatus.toLowerCase()) {
                        case 'pending':
                            badgeClass = 'bg-warning text-dark';
                            break;
                        case 'processing':
                            badgeClass = 'bg-primary text-white';
                            break;
                        case 'for pickup':
                            badgeClass = 'bg-success text-white';
                            break;
                        case 'rejected':
                            badgeClass = 'bg-danger text-white';
                            break;
                        case 'released':
                            badgeClass = 'bg-success text-white';
                            break;
                        default:
                            badgeClass = 'bg-secondary text-white';
                    }
                    return `
                        <span class="badge ${badgeClass}">
                            ${status.toUpperCase()}
                        </span>
                    `;
                }
            })
        }

        if (datatable.hasClass('with-actions')) {
            columns.push({
                data: null,
                title: 'Actions',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: (_, __, row) => {
                    const routeValue = row.show_route || '#';
                    const isValidPath = routeValue.startsWith('/') && !routeValue.startsWith('//');
                    const isSameOrigin = routeValue.startsWith(window.location.origin);
                    if (!isValidPath && !isSameOrigin) {
                        console.warn(`Invalid route: ${routeValue}`);
                        return '';
                    }
                    const safeRoute = encodeURI(routeValue);
                    return ` 
                        <a href="${safeRoute}" class="btn btn-sm btn-primary">
                            <i class="ki-duotone ki-eye pe-0">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </a>
                    `;
                }
            })
        }

        $('.data-table').DataTable({
            responsive: true,
            ordering: true,
            columns: columns,
            order: [],
            ajax: route ? {
                url: route,
                type: 'GET',
                dataSrc: 'data',
                headers: {
                    'Accept': 'application/json'
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
                    className: 'btn btn-primary py-1 px-3',
                },
                {
                    extend: 'print',
                    text: 'Print',
                    className: 'btn btn-secondary py-1 px-3 ml-2',
                },
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

        if (route) {
            if (window.datatableRefreshInterval) {
                clearInterval(window.datatableRefreshInterval);
            }

            window.datatableRefreshInterval = setInterval(() => {
                datatable.DataTable().ajax.reload(null, false);
            }, 10000);
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
</script>
