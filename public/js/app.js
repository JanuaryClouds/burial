/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";

import updateDistrict from './districts.js';

function randomizeMulticolorBorder() {
    document.querySelectorAll('.card.multicolor-border').forEach(card => {
        const redEnd = Math.floor(Math.random() * 40) + 20;
        const yellowEnd = Math.floor(Math.random() * (90 - redEnd)) + redEnd;

        card.style.setProperty('--red-end', `${redEnd}%`);
        card.style.setProperty('--yellow-end', `${yellowEnd}%`);
    });
}

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

    const whiteBackgroundPlugin = {
        id: 'whiteBackground',
    
        beforeDraw(chart, args, options) {
            const { ctx, width, height } = chart;
    
            ctx.save();
            ctx.globalCompositeOperation = 'destination-over';
            ctx.fillStyle = options.color || '#ffffff';
            ctx.fillRect(0, 0, width, height);
            ctx.restore();
        }
    };
    
    Chart.register(whiteBackgroundPlugin);

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
                   plugins: {
                    whiteBackground: {
                        color: '#ffffff'
                    },
                    legend: {
                        display: true,
                        position: 'bottom',
                    }
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
                    plugins: {
                        whiteBackground: {
                            color: '#ffffff'
                        },
                        legend: {
                            display: false,
                        }
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
                        whiteBackground: {
                            color: '#ffffff'
                        },
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

// Initialize select2s as soon as this module runs. Module scripts execute after
// the DOM is parsed but BEFORE the DOMContentLoaded event, so this beats
// Metronic's own createSelect2 (scripts.bundle.js), which would otherwise
// initialize our selects first WITHOUT the tags option and mark them
// data-kt-initialized, making initSelect2 skip them.
initSelect2();

document.addEventListener('DOMContentLoaded', () => {
    checkAndRenderCharts();
    randomizeMulticolorBorder();
    initSelect2();
    
    $('#barangay_id').on('change', function() {
        let text = $(this).find('option:selected').text();
        updateDistrict(text.trim());
    });

    $('#client_uuid_select').on('change', function(event) {
        const uuid = $(this).val();
        Livewire.dispatch('client-selected', uuid);
    });
    $('#beneficiary_uuid_select').on('change', function(event) {
        const uuid = $(this).val();
        Livewire.dispatch('beneficiary-selected', uuid);
    });
});

function initSelect2(root = document) {
    if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
        return;
    }

    root.querySelectorAll('[data-control="select2"], [data-kt-select2="true"]').forEach((element) => {
        if (element.getAttribute('data-kt-initialized') === '1') {
            return;
        }

        // If Livewire morphed this element in place (without wire:ignore), the
        // previous select2 instance and its container may still be attached.
        // Tear those down first so we never end up with a stale instance or
        // duplicate dropdown container.
        if ($(element).data('select2')) {
            $(element).select2('destroy');
        }

        let sibling = element.nextElementSibling;
        while (sibling && sibling.classList.contains('select2-container')) {
            const next = sibling.nextElementSibling;
            sibling.remove();
            sibling = next;
        }

        const options = {
            dir: document.body.getAttribute('direction'),
        };

        if (element.getAttribute('data-hide-search') === 'true') {
            options.minimumResultsForSearch = Infinity;
        }

        if (element.classList.contains('select-dynamic')) {
            options.tags = true;
        }

        $(element).select2(options);

        // Handle Select2's KTMenu parent case
        if (element.hasAttribute('data-dropdown-parent') && element.hasAttribute('multiple')) {
            const parentEl = document.querySelector(element.getAttribute('data-dropdown-parent'));

            if (parentEl && parentEl.hasAttribute('data-kt-menu') && typeof KTMenu !== 'undefined') {
                const menu = new KTMenu(parentEl);

                if (menu) {
                    $(element).on('select2:unselect', function () {
                        element.setAttribute('data-multiple-unselect', '1');
                    });

                    menu.on('kt.menu.dropdown.hide', function (item) {
                        if (element.getAttribute('data-multiple-unselect') === '1') {
                            element.removeAttribute('data-multiple-unselect');
                            return false;
                        }
                    });
                }
            }
        }

        element.setAttribute('data-kt-initialized', '1');
    });
}

// Expose for vanilla JS that adds dynamic selects (e.g. the beneficiary family
// composition rows) so newly inserted dropdowns can be initialized too.
window.initSelect2 = initSelect2;

document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.updated', ({ el }) => {
        requestAnimationFrame(() => {
            initSelect2(el);
            randomizeMulticolorBorder();
        });
    });

    Livewire.hook('morph.added', ({ el }) => {
        requestAnimationFrame(() => {
            initSelect2(el);
        });
    });

    // `morphed` fires after every element in a component has been morphed,
    // including lazy/deferred children whose HTML is loaded separately. It is
    // the most reliable place to initialize select2 for freshly added selects.
    Livewire.hook('morphed', ({ el }) => {
        requestAnimationFrame(() => {
            initSelect2(el);
        });
    });

    Livewire.hook('element.init', ({ el }) => {
        requestAnimationFrame(() => {
            initSelect2(el);
        });
    });
});

document.addEventListener('livewire:navigated', () => {
    initSelect2();
});

$(document).ajaxError(function(event, xhr) {
    if (xhr.status === 403) {
        window.location.reload();
    }
});

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
