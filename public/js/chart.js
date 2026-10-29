function getChartPalette() {
    const styles = getComputedStyle(document.documentElement);

    return Array.from({ length: 16 }, (_, index) =>
        styles.getPropertyValue(`--chart-color-${index + 1}`).trim()
    ).filter(Boolean);
}

/* Chart.js draws onto canvas, so CSS colors must be applied to each dataset.
 * Keep a label-to-color map per chart so filtering/reordering retains colors.
 */
const taguigPalettePlugin = {
    id: 'taguigPalette',
    beforeUpdate(chart) {
        const palette = getChartPalette();
        if (!palette.length) return;

        if (chart.config.type === 'line') {
            chart.data.datasets.forEach(dataset => {
                dataset.borderColor = palette[0];
                dataset.backgroundColor = palette[0];
                dataset.pointBackgroundColor = palette[0];
            });
            return;
        }

        const colorsByLabel = chart.$taguigColors || (chart.$taguigColors = new Map());
        const colors = chart.data.labels.map(label => {
            const key = String(label);
            if (!colorsByLabel.has(key)) {
                const index = colorsByLabel.size;
                // Extend large category lists without cycling the original colors.
                const color = palette[index] ||
                    `hsl(${((index - palette.length) * 137.508 + 12) % 360}, 55%, ${index % 2 ? 42 : 58}%)`;
                colorsByLabel.set(key, color);
            }
            return colorsByLabel.get(key);
        });

        chart.data.datasets.forEach(dataset => {
            dataset.backgroundColor = colors;
            dataset.borderColor = '#ffffff';
        });
    }
};

function checkCharts() {
    window.renderedCharts = window.renderedCharts || [];
    const canvases = document.querySelectorAll('canvas')

    canvases.forEach(canvas => {
        const chartId = canvas.id;
        if (!chartId) return;

        const chartData = JSON.parse(canvas.dataset.chartData);
        const chartLabels = JSON.parse(canvas.dataset.chartLabels);
        const chartTitle = canvas.dataset.chartTitle || '';
        const chartType = canvas.dataset.chartType;

        if (!chartData || !chartLabels) return;

        renderChart(chartType, chartData, chartId, chartLabels, chartTitle);

        const uriInput = document.getElementById(`${chartId}_uri`);

        if (uriInput) {
            uriInput.value = canvas.toDataURL('image/png');
            uriInput.dispatchEvent(new Event('input', { bubbles:true }));
        }
    });

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
}

function renderChart(chartType, chartData, chartId, chartLabels, chartTitle) {
    let chart = null;

    if (chartType === 'pie') {
        chart = renderPieChart(chartData, chartId, chartLabels, chartTitle);
    } else if (chartType === 'line') {
        chart = renderLineChart(chartData, chartId, chartLabels, chartTitle);
    } else if (chartType === 'bar') {
        chart = renderBarChart(chartData, chartId, chartLabels, chartTitle);
    }

    if (chart) {
        window.renderedCharts.push(chart);
    }
}

function renderPieChart(chartData, chartId, chartLabels, chartTitle) {
    const piChart = document.getElementById(chartId);
    if (piChart) {
        const chart = new Chart(piChart, {
            type: 'pie',
            plugins: [taguigPalettePlugin],
            data: {
                labels: chartLabels,
                datasets: [{
                    data: chartData,
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

        return chart;
    }
}

function renderLineChart(chartData, chartId, chartLabels, chartTitle) {
    const lineChart = document.getElementById(chartId);
    if (lineChart) {
        const chart = new Chart(lineChart, {
            type: 'line',
            plugins: [taguigPalettePlugin],
            data: {
                labels: chartLabels,
                datasets: [{
                    data: chartData,
                    fill: false,
                    tension: 0.1
                }],
                borderWidth: 1
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                // Only show the label if it's an integer
                                if (value % 1 === 0) {
                                    return value;
                                }
                            }
                        },
                        beginAtZero: true,
                    },
                },
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

        return chart;
    }
}

function renderBarChart(chartData, chartId, chartLabels, chartTitle) {
    const barChart = document.getElementById(chartId);
    if (barChart) {
        const chart = new Chart(barChart, {
            type: 'bar',
            plugins: [taguigPalettePlugin],
            data: {
                labels: chartLabels,
                datasets: [{
                    data: chartData,
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
        });

        return chart;
    }
}

function refreshChart() {
    Livewire.on('refresh-chart', (event) => {
        let data = event[0];
        let chart = Chart.getChart(data.chartId);

        if (!chart) return;

        chart.data.datasets[0].data = data.chartData.count;
        chart.data.labels = data.chartData.labels;
        chart.update('none');
    });
}

function getChartImage(chartId) {
    let chart = window.renderedCharts[chartId];

    if (!chart) return;

    return chart.toDataURL('image/png');
}

function collectCharts() {
    let charts = window.renderedCharts;

    const chartImages = Object.fromEntries(
        Object.entries(charts)
            .map(([id, chart]) => [
                id,
                chart.canvas.toDataURL('image/png')
            ])
    );

    return chartImages;
}

export {checkCharts, refreshChart, getChartImage, collectCharts};
