export function checkAndRenderDataTables() {
    $(document).ready(function () {
        console.log("Initializing DataTable...");
        $('#applications-table').DataTable({
            responsive: true,
            ordering: true, // keep ordering functional
            dom:
                // First row: buttons on the left, filter on the right
                // TODO: Move buttons to the right and be green
                "<'row mb-2'<'col-sm-6 d-flex align-items-center'l<'mr-3'>><'col-sm-6 d-flex justify-content-end'fB>>" +
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
                {
                    text: 'Excel',
                    action: function() {
                        const url = window.location.href;
                        const status = url.substring(url.lastIndexOf('/') + 1);
                        window.location.href = '/applications/export'
                    }
                }
            ],
            columnDefs: [
                { orderable: false, targets: [4] } // disable sorting on the Actions column
            ],
            classes: {
                sortAsc: '',     // override ascending class
                sortDesc: '',    // override descending class
                sortable: ''     // override neutral sortable class
            }
        });
        
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
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            classes: {
                sortAsc: '',     // override ascending class
                sortDesc: '',    // override descending class
                sortable: ''     // override neutral sortable class 
            }
        })
        
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
                'copy', 'csv', 'excel', 'pdf', 'print'
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

        $('#generic-table').DataTable({
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
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            classes: {
                sortAsc: '',     // override ascending class
                sortDesc: '',    // override descending class
                sortable: ''     // override neutral sortable class 
            }
        })
    });
}