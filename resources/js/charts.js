import Chart from 'chart.js';

export function checkAndRenderCharts() {
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
                    legend: {
                        display: false,
                    },
                    title: {
                        display: !!chartTitle,
                        text: chartTitle
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize: 1,  // force steps of 1
                                callback: function(value) {
                                    return Number.isInteger(value) ? value : null; // only show whole numbers
                                }
                            }
                        }]
                    }
                }
            })
            
            window.renderedCharts[chartId] = chart;
        }
    }
}
