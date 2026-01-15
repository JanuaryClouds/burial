/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";

import $ from "jquery";
window.$ = window.jQuery = $;

import DataTable from "datatables.net-responsive-bs4";
import "datatables.net-bs4/css/dataTables.bootstrap4.min.css";
import 'datatables.net-buttons-bs4';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import 'datatables.net-responsive-bs4';
import { checkAndRenderCharts } from "./charts";
import { checkAndRenderDataTables } from "./dataTables";
document.addEventListener('DOMContentLoaded', () => {
    checkAndRenderCharts();
    checkAndRenderDataTables();
})

import jszip from 'jszip';
import pdfmake from 'pdfmake';

DataTable.Buttons.jszip(jszip);
DataTable.Buttons.pdfMake(pdfmake);

$(document).ready(function () {
    $(".nav-link.has-dropdown").each(function () {
        const $this = $(this).parent();
        if ($this.hasClass("active") && $(window).width() > 768) {
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

import Alpine from "alpinejs";
import persist from "@alpinejs/persist";
Alpine.plugin(persist);

import "./autofill";
window.Alpine = Alpine;
Alpine.start();
