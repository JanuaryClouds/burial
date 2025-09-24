import Chart from 'chart.js';

export function renderPieChart(chartData, chartId, chartLabels) {
    const piChart = document.getElementById(chartId);
    if (piChart) {
        new Chart(piChart, {
           type: 'pie', 
           data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Applications',
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
                    display: true,
                    text: 'Burial Assistance Requests by Barangay'
                }
           }
        });
    }
}