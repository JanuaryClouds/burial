export function checkAndRenderDataTables() {
    $(document).ready(function () {
        const cmsTable = $('#cms-table');

        cmsTable ?? cmsTable.DataTable({
            responsive: true,
            ordering: true,
            dom: 
                // First row: buttons on the left, filter on the right
                "<'row mb-2'<'col-sm-6 d-flex align-items-center'l><'col-sm-6 d-flex justify-content-end'f<'mr-3 me-3'>B>>" +
                // Table
                "<'row'<'col-12'tr>>" +
                // Bottom row: info and pagination
                "<'row mt-2'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",
            buttons:[
                {
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
                sortAsc: '',     // override ascending class
                sortDesc: '',    // override descending class
                sortable: ''     // override neutral sortable class 
            }
        })

        const perBarangayTable = $('#per-barangay-table');
        
        // Cannot have export options because of the row grouping
        perBarangayTable ?? perBarangayTable.DataTable({
            responsive: true,
            ordering: true,
            rowGroup: {
                dataSrc: 0
            },
            order:[[0, 'asc']],
            dom:
                // First row: buttons on the left, filter on the right
                "<'row mb-2'<'col-sm-6 d-flex align-items-center'l<'mr-3'>><'col-sm-6 d-flex justify-content-end'f>>" +
                // Table
                "<'row'<'col-12'tr>>" +
                // Bottom row: info and pagination
                "<'row mt-2'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",
            // buttons:[
            //     'copy', 'csv', 'excel', 'pdf', 'print'
            // ],
            classes: {
                sortAsc: '',     // override ascending class
                sortDesc: '',    // override descending class
                sortable: ''     // override neutral sortable class 
            }
        })

        const assignmentTable = $('#assignment-table');

        assignmentTable ?? assignmentTable.DataTable({
            responsive: true,
            order: [[3, 'desc']],
            ordering: true,
            dom: 
                // First row: buttons on the left, filter on the right
                "<'row mb-2'<'col-sm-6 d-flex align-items-center'l<'mr-3'>><'col-sm-6 d-flex justify-content-end'f>>" +
                // Table
                "<'row'<'col-12'tr>>" +
                // Bottom row: info and pagination
                "<'row mt-2'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",
            buttons:[
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            classes: {
                sortAsc: '',     // override ascending class
                sortDesc: '',    // override descending class
                sortable: ''     // override neutral sortable class 
            }
        })

        const assignedApplicationsTable = $('#assigned-applications-table');

        assignedApplicationsTable ?? assignedApplicationsTable.DataTable({
            responsive: true,
            ordering: true,
            dom: 
                // First row: buttons on the left, filter on the right
                "<'row mb-2'<'col-sm-6 d-flex align-items-center'l<'mr-3'>><'col-sm-6 d-flex justify-content-end'f>>" +
                // Table
                "<'row'<'col-12'tr>>" +
                // Bottom row: info and pagination
                "<'row mt-2'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",
            buttons:[
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            classes: {
                sortAsc: '',     // override ascending class
                sortDesc: '',    // override descending class
                sortable: ''     // override neutral sortable class 
            }
        })

        const latestApplicationsTable = $('#latest-applications-table');

        latestApplicationsTable ?? latestApplicationsTable.DataTable({
            responsive: true,
            ordering: true,
            order:[[6, 'desc']], // order by Submitted on column descending
            dom: 
                // First row: buttons on the left, filter on the right
                "<'row mb-2'<'col-sm-6 d-flex align-items-center'i<'mr-3'>><'col-sm-6 d-flex justify-content-end'f>>" +
                // Table
                "<'row'<'col-12'tr>>",
                // Bottom row: info
                // "<'row mt-2'<'col-sm-6'i>>"
            buttons:[
                {
                    extend: 'excel',
                    text: '<i class="mr-2 fas fa-file-excel"></i> Export to Excel',
                    className: 'btn btn-primary py-1 px-3',
                },
                {
                    extend: 'print',
                    text: '<i class="mr-2 fas fa-print"></i> Print',
                    className: 'btn btn-secondary py-1 px-3 ml-2',
                },
                // 'copy', 
                // 'csv', 
                // 'pdf',
                // 'print'
            ],
            classes: {
                sortAsc: '',     // override ascending class
                sortDesc: '',    // override descending class
                sortable: ''     // override neutral sortable class 
            }
        })

        const genericTable = $('.generic-table');

        genericTable ?? genericTable.each(function() {
            this.DataTable({
                responsive: true,
                ordering: true,
                dom: 
                    // First row: buttons on the left, filter on the right
                    "<'row mb-2'<'col-sm-6 d-flex align-items-center'l><'col-sm-6 d-flex justify-content-end'f<'mr-3 me-3'>B>>" +
                    // Table
                    "<'row'<'col-12'tr>>" +
                    // Bottom row: info and pagination
                    "<'row mt-2'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",
                buttons:[
                    {
                        extend: 'excel',
                        text: 'Export to Excel',
                        className: 'btn btn-primary py-1 px-3'
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        className: 'btn btn-secondary py-1 px-3 ml-2'
                    },
                    // 'copy', 
                    // 'csv', 
                    // 'excel', 
                    // 'pdf', 
                    // 'print'
                ],
                classes: {
                    sortAsc: '',     // override ascending class
                    sortDesc: '',    // override descending class
                    sortable: ''     // override neutral sortable class 
                }
            })
        });

        const reportsTable = $('#reports-applications-table');
        reportsTable ?? reportsTable.DataTable({
            responsive: true,
            ordering: true, // keep ordering functional
            dom:
                // First row: buttons on the left, filter on the right
                "<'row mb-2'<'col-sm-6 d-flex align-items-center'l<'mr-3'>><'col-sm-6 d-flex justify-content-end align-items-center'f<'ml-3'>B>>" +
                // Table
                "<'row'<'col-12'tr>>" +
                // Bottom row: info and pagination
                "<'row mt-2'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",
            buttons: [
                // 'copy',
                // 'csv',
                // 'excel',
                // 'pdf',
                // 'print'
            ],
            classes: {
                sortAsc: '',     // override ascending class
                sortDesc: '',    // override descending class
                sortable: ''     // override neutral sortable class
            }
        });;
    });
}