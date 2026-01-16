/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";

function checkAndRenderCharts() {
    window.renderedCharts = window.renderedCharts || {};
    const canvases = document.querySelectorAll('canvas')

    canvases.forEach(canvas => {
        const chartId = canvas.id;
        if (!chartId) return;

        const chartData = JSON.parse(canvas.dataset.chartData);
        const chartLabels = JSON.parse(canvas.dataset.chartLabels);
        const chartTitle = canvas.dataset.chartTitle || '';
        const chartType = canvas.dataset.chartType;
        renderChart(chartType, chartData, chartId, chartLabels, chartTitle);
    });

    function renderChart(chartType, chartData, chartId, chartLabels, chartTitle) {
        if (chartType === 'pie') {
            renderPieChart(chartData, chartId, chartLabels, chartTitle);
        } else if (chartType === 'line') {
            renderLineChart(chartData, chartId, chartLabels, chartTitle);
        } else if (chartType === 'bar') {
            renderBarChart(chartData, chartId, chartLabels, chartTitle);
        }
    }

    function renderPieChart(chartData, chartId, chartLabels, chartTitle) {
        const piChart = document.getElementById(chartId);
        if (piChart) {
            const chart = new Chart(piChart, {
               type: 'pie', 
               data: {
                    labels: chartLabels,
                    datasets: [{
                        data: chartData,
                        backgroundColor: [
                            '#fbbf24', '#f87171', '#60a5fa', '#34d399', '#a78bfa',
                            '#f472b6', '#38bdf8', '#4ade80', '#facc15', '#818cf8'
                        ],
                        borderWidth: 1
                    }]
               },
               options: {
                   responsive: true,
                    legend: {
                        display: true,
                        position: 'bottom',
                    },
                    title: {
                        display: !!chartTitle,
                        text: chartTitle
                    }
               }
            });

            window.renderedCharts[chartId] = chart;
        }
    }

    function renderLineChart(chartData, chartId, chartLabels, chartTitle) {
        const lineChart = document.getElementById(chartId);
        if (lineChart) {
            const chart = new Chart(lineChart, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        data: chartData,
                        fill: false,
                        borderColor: '#3b82f6',
                        tension: 0.1
                    }],
                    borderWidth: 1
                },
                options: {
                    responsive: true,
                    legend: {
                        display: false,
                    },
                    title: {
                        display: !!chartTitle,
                        text: chartTitle
                    }
                }
            })
            
            window.renderedCharts[chartId] = chart;
        }
    }

    function renderBarChart(chartData, chartId, chartLabels, chartTitle) {
        const barChart = document.getElementById(chartId);
        console.log(chartLabels);
        if (barChart) {
            const chart = new Chart(barChart, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        data: chartData,
                        backgroundColor: 
                            ['#3b82f6', '#f87171', '#34d399', '#fbbf24', '#a78bfa',
                             '#f472b6', '#38bdf8', '#4ade80', '#facc15', '#818cf8',
                             '#e879f9', '#60a5fa', '#faccd2', '#f47236', '#428cf8'],
                        borderWidth: 1
                    }],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                generateLabels: function(chart) {
                                    return chart.data.labels.map((label, index) => ({
                                        text: label,
                                        fillStyle: chart.data.datasets[0].backgroundColor[index],
                                        index,
                                    }));
                                }
                            },
                        },
                        title: {
                            display: !!chartTitle,
                            text: chartTitle
                        },
                    },
                    scales: {
                        y: {
                            title: {
                                display: true,
                                text: 'Quantity'
                            },
                            min: 0,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            })
            
            window.renderedCharts[chartId] = chart;
        }
    }
}

function checkAndRenderDataTables() {
    const cmsTable = $('#cms-table');

    if (!cmsTable) return;
    cmsTable.DataTable({
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
    });

    const perBarangayTable = $('#per-barangay-table');
    
    // Cannot have export options because of the row grouping
    if (!perBarangayTable) return;
    perBarangayTable.DataTable({
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
    });

    const assignmentTable = $('#assignment-table');
    if (!assignmentTable) return;
    assignmentTable.DataTable({
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
    });

    const assignedApplicationsTable = $('#assigned-applications-table');
    if (!assignedApplicationsTable) return;
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
    });

    const latestApplicationsTable = $('#latest-applications-table');
    if (!latestApplicationsTable) return;
    latestApplicationsTable.DataTable({
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
    });

    const genericTables = $('.generic-table');
    if (genericTables.length === 0) return;

    genericTables.each(function() { 
        $(this).DataTable({ 
            responsive: true,
            ordering: true,
            dom: 
                // First row: buttons on the left, filter on the right
                "<'row mb-2'<'col-sm-6 d-flex align-items-center'l><'col-sm-6 d-flex justify-content-end'f<'mr-3 me-3'>B>>" +
                // Table
                "<'row'<'col-12'tr>>" +
                // Bottom row: info and pagination
                "<'row mt-2'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",
            buttons: [
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
                // Other button options can be included here
            ],
            classes: {
                sortAsc: '',     // Override ascending class
                sortDesc: '',    // Override descending class
                sortable: ''     // Override neutral sortable class 
            }
        });
    });

    const reportsTable = $('#reports-applications-table');
    if (!reportsTable) return;
    reportsTable.DataTable({
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
}


document.addEventListener('DOMContentLoaded', () => {
    checkAndRenderCharts();
    checkAndRenderDataTables();
})

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

Alpine.plugin(persist);
window.Alpine = Alpine;
Alpine.start();