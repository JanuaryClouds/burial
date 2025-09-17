// 1. Core jQuery first
import $ from "jquery";
window.$ = window.jQuery = $;

// 2. Required dependencies (use Bootstrap bundle so Popper is included)
import "bootstrap/dist/js/bootstrap.bundle";


// 4. DataTables (depends on jQuery)
import jszip from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from "datatables.net-responsive-bs4";
import "datatables.net-bs4/css/dataTables.bootstrap4.min.css";
import 'datatables.net-buttons-bs4';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import 'datatables.net-responsive-bs4';

DataTable.Buttons.jszip(jszip);
DataTable.Buttons.pdfMake(pdfmake);

// 3. jQuery plugins (must come AFTER jQuery is set globally)
import "jquery.nicescroll";   // Stisla requires this
import "moment";              // Stisla also uses moment

$(document).ready(function () {
    console.log("Initializing DataTable...");
    $('#applications-table').DataTable({
        responsive: true,
        ordering: true, // keep ordering functional
        dom:
            // First row: buttons on the left, filter on the right
            "<'row mb-2'<'col-sm-6 d-flex align-items-center'l<'mr-3'>B><'col-sm-6 d-flex justify-content-end'f>>" +
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
                text: 'Export to Excel',
                action: function() {
                    window.location.href = '/admin/applications/history/export'
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
});

console.log("DataTables is running:", $.fn.dataTable ? true : false);

// 6. AlpineJS (doesn’t depend on jQuery)
import Alpine from "alpinejs";
import persist from "@alpinejs/persist";
Alpine.plugin(persist);
window.Alpine = Alpine;
Alpine.start();
