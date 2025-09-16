// 1. Core jQuery first
import $ from "jquery";
window.$ = window.jQuery = $;

// 2. Required dependencies (use Bootstrap bundle so Popper is included)
import "bootstrap/dist/js/bootstrap.bundle";


// 4. DataTables (depends on jQuery)
import "datatables.net-bs4";
import "datatables.net-bs4/css/dataTables.bootstrap4.min.css";
import "datatables.net-responsive-bs4";
import "datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css";

// 3. jQuery plugins (must come AFTER jQuery is set globally)
import "jquery.nicescroll";   // Stisla requires this
import "moment";              // Stisla also uses moment

$(document).ready(function () {
    console.log("Initializing DataTable...");
    $('#applications-table').DataTable({
        responsive: true,
        ordering: true, // keep ordering functional
        // dom: 'lrtip',   // optional: controls what UI elements appear
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
