export function checkAndRenderDataTables() {
    $(document).ready(function () {
        console.log("Initializing DataTable...");
        $('#cms-table').DataTable({
            responsive: true,
            ordering: true,
            dom: 
                // First row: buttons on the left, filter on the right
                "<'row mb-2'<'col-sm-6 d-flex align-items-center'l<'mr-3'>B><'col-sm-6 d-flex justify-content-end'f>>" +
                // Table
                "<'row'<'col-12'tr>>" +
                // Bottom row: info and pagination
                "<'row mt-2'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",
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
        
        // Cannot have export options because of the row grouping
        $('#per-barangay-table').DataTable({
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

        // ! Unused table
        $('#applications-per-encoder-table').DataTable({
            responsive: true,
            ordering: true,
            dom: 
                // First row: buttons on the left, filter on the right
                "<'row mb-2'<'col-sm-6 d-flex align-items-center'l<'mr-3'>B><'col-sm-6 d-flex justify-content-end'f>>" +
                // Table
                "<'row'<'col-12'tr>>" +
                // Bottom row: info and pagination
                "<'row mt-2'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",
            buttons:[
                'copy',
                'csv', 
                'excel', 
                'pdf', 
                'print'
            ],
            classes: {
                sortAsc: '',     // override ascending class
                sortDesc: '',    // override descending class
                sortable: ''     // override neutral sortable class 
            }
        })

        $('#assignments-table').DataTable({
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

        $('#assigned-applications-table').DataTable({
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

        $('#latest-applications-table').DataTable({
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

        $('.generic-table').each(function() {
            $(this).DataTable({
                responsive: true,
                ordering: true,
                dom: 
                    // First row: buttons on the left, filter on the right
                    "<'row mb-2'<'col-sm-6 d-flex align-items-center'l<'mr-3'>B><'col-sm-6 d-flex justify-content-end'f>>" +
                    // Table
                    "<'row'<'col-12'tr>>" +
                    // Bottom row: info and pagination
                    "<'row mt-2'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",
                buttons:[
                    {
                        extend: 'excel',
                        text: '<i class="mr-2 fa fa-file-excel"></i>Export to Excel',
                        className: 'btn btn-primary py-1 px-3'
                    },
                    {
                        extend: 'print',
                        text: '<i class="mr-2 fa fa-print"></i>Print',
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

        $('#reports-applications-table').DataTable({
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