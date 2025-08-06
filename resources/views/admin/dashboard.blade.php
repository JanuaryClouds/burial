@extends('layouts.admin')
@section('content')
@section('breadcrumb')
    <x-breadcrumb :items="[
        "label" => "Dashboard"
        ]"/>
@endsection
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<title>Dashboard</title>
<h4 class="g-0">Dashboard</h4>
<div class="row d-flex flex-column justify-content-start gap-4">
    <div class="row d-flex flex-nowrap gap-3 justify-content-start align-items-center gap-2 w-100">
        <div class="col-3 bg-white d-flex justify-content-start rounded shadow-sm g-0">
            <span class="col-4 d-flex rounded align-items-center justify-content-center me-2 g-0 fs-3 text-white" style="background-color: #F4C027;">
                <i class="fa-solid fa-bell-concierge"></i>
            </span>
            <span class="col-9 d-flex flex-column justify-content-between p-2">
                <h4 class="">Requests</h4>
                <p class="fw-semibold">{{ $serviceRequests->count() }}</p>
            </span>
        </div>
        <div class="col-4 bg-white d-flex justify-content-start rounded shadow-sm g-0">
            <span class="col-3 d-flex rounded align-items-center justify-content-center me-2 g-0 fs-3 text-white" style="background-color: #8ec5ff;">
                <i class="fa-solid fa-building"></i>
            </span>
            <span class="col-9 d-flex flex-column justify-content-between p-2">
                <h4 class="">Service Providers</h4>
                <p class="fw-semibold">{{ $providers->count() }}</p>
            </span>
        </div>
        <div class="col-4 bg-white d-flex justify-content-start rounded shadow-sm g-0">
            <span class="col-3 d-flex rounded align-items-center justify-content-center me-2 g-0 fs-3 text-white" style="background-color: #ffa2a2;">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </span>
            <span class="col-8 d-flex flex-column justify-content-between p-2">
                <h4 class="">Records</h2>
                <p class="fw-semibold">{{ $services->count() }}</p>
            </span>
        </div>
    </div>
    
    <h4 class="g-0">Charts</h4>
    <div class="row justify-content-center align-items-center gap-2">
        <span class="col">
            <canvas id="requestsDistributionChart"></canvas>
        </span>
        <span class="col">
            <canvas id="providersDistributionChart"></canvas>
        </span>
        <span class="col">
            <canvas id="servicesDistributionChart"></canvas>
        </span>
    </div>
    
    <div
    class="row flex-column gap-2 g-0"
    >
    <h4 class="fw-bold">Approved Burial Requests</h4>
    <p class="">These are approved burial assistance requests. Please take note of the duration of the requests' burial. Generate a burial service form once the burial has finished.</p>
    <x-approved-requests-board />
</div>

<script>
        const requestsDistribution = document.getElementById('requestsDistributionChart').getContext('2d');
        const requestsChartData = {
            labels: {!! json_encode($requestsData->pluck('name')) !!},
            datasets: [{
                label: 'Number of Requests',
                data: {!! json_encode($requestsData->pluck('count')) !!},
                backgroundColor: [
                    '#fbbf24', '#f87171', '#60a5fa', '#34d399', '#a78bfa',
                    '#f472b6', '#38bdf8', '#4ade80', '#facc15', '#818cf8'
                ],
                borderWidth: 1
            }]
        };

        const providersDistribution = document.getElementById('providersDistributionChart').getContext('2d');
        const providersChartData = {
            labels: {!! json_encode($providersData->pluck('name')) !!},
            datasets: [{
                label: 'Number of Providers',
                data: {!! json_encode($providersData->pluck('count')) !!},
                backgroundColor: [
                    '#8ec5ff', '#f87171', '#60a5fa', '#34d399', '#a78bfa',
                    '#f472b6', '#38bdf8', '#4ade80', '#facc15', '#818cf8'
                ],
                borderWidth: 1
            }]
        };

        const servicesDistribution = document.getElementById('servicesDistributionChart').getContext('2d');
        const servicesChartData = {
            labels: {!! json_encode($servicesData->pluck('name')) !!},
            datasets: [{
                label: 'Number of Services',
                data: {!! json_encode($servicesData->pluck('count')) !!},
                backgroundColor: [
                    '#ffa2a2', '#60a5fa', '#34d399', '#fbbf24', '#a78bfa',
                    '#f472b6', '#38bdf8', '#4ade80', '#facc15', '#818cf8'
                ],
                borderWidth: 1
            }]
        };

        const requestsDataConfig = {
            type: 'pie',
            data: requestsChartData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Burial Assistance Requests by Barangay'
                    }
                }
            }
        };

        const providersDataConfig = {
            type: 'pie',
            data: providersChartData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Burial Service Providers by Barangay'
                    }
                }
            }
        };

        const servicesChartConfig = {
            type: 'pie',
            data: servicesChartData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Burial Services Recorded by Barangay'
                    }
                }
            }
        };

        new Chart(requestsDistribution, requestsDataConfig);
        new Chart(providersDistribution, providersDataConfig);
        new Chart(servicesDistribution, servicesChartConfig);
    </script>
</div>
</div>

@endsection