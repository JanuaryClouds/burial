// 1. Core jQuery first
import $ from "jquery";
window.$ = window.jQuery = $;

// 2. Required dependencies (use Bootstrap bundle so Popper is included)
import "bootstrap/dist/js/bootstrap.bundle.min.js";

// 3. jQuery plugins (must come AFTER jQuery is set globally)
import "popper.js";
import "jquery.nicescroll";   // Stisla requires this
import "moment";              // Stisla also uses moment
import { checkAndRenderCharts } from "./charts";
import { checkAndRenderDataTables } from "./dataTables";
document.addEventListener('DOMContentLoaded', () => {
    checkAndRenderCharts();
    checkAndRenderDataTables();
})

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


console.log("DataTables is running:", $.fn.dataTable ? true : false);

$(document).ready(function () {
    $(".nav-link.has-dropdown").each(function () {
        const $this = $(this).parent();
        if ($this.hasClass("active")) {
            $this.find(".dropdown-menu").slideToggle();
        }
    });

    $(".nav-link.has-dropdown").on("click", function (e) {
        const $this = $(this).parent();
        $this.find(".dropdown-menu").slideToggle(200);
        e.preventDefault();
        e.stopPropagation();
    });
});


// 6. AlpineJS (doesn’t depend on jQuery)
import Alpine from "alpinejs";
import persist from "@alpinejs/persist";
Alpine.plugin(persist);

import "./autofill";
window.Alpine = Alpine;
Alpine.start();
