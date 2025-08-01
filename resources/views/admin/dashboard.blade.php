@extends('layouts.admin')
@section('content')
@section('breadcrumb')
    <x-breadcrumb :items="[]"/>
@endsection
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<title>Dashboard</title>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="bg-white flex justify-start rounded shadow">
        <span class="h-full aspect-square bg-yellow-300 flex rounded items-center justify-center mr-2">
            <i class="fa-solid fa-bell-concierge text-4xl"></i>
        </span>
        <span class="flex flex-col justify-between p-4">
            <h2 class="font-bold text-gray-700 text-2xl">{{ $serviceRequests->count() }}</h2>
            <p class="text-sm text-gray-500 mt-2">Burial Assistance Requests are waiting for approval</p>
        </span>
    </div>
    <div class="bg-white flex justify-start rounded shadow">
        <span class="h-full aspect-square bg-blue-300 flex rounded items-center justify-center mr-2">
            <i class="fa-solid fa-building text-4xl"></i>
        </span>
        <span class="flex flex-col justify-between p-4">
            <h2 class="font-bold text-gray-700 text-2xl">{{ $providers->count() }}</h2>
            <p class="text-sm text-gray-500 mt-2">Burial Service Providers Listed</p>
        </span>
    </div>
    <div class="bg-white flex justify-start rounded shadow">
        <span class="h-full aspect-square bg-red-300 flex rounded items-center justify-center mr-2">
            <i class="fa-solid fa-clock-rotate-left text-4xl"></i>
        </span>
        <span class="flex flex-col justify-between p-4">
            <h2 class="font-bold text-gray-700 text-2xl">{{ $services->count() }}</h2>
            <p class="text-sm text-gray-500 mt-2">Burials serviced and recorded</p>
        </span>
    </div>
</div>

<div class="bg-white rounded shadow p-4">
    <h2 class="font-bold text-gray-700">Charts</h2>
    <div class="mt-4 w-full h-full flex justify-start">
        <span class="w-96 h-96">
            <canvas id="requestsDistributionChart"></canvas>
        </span>
        <span class="w-96 h-96">
            <canvas id="providersDistributionChart"></canvas>
        </span>
        <span class="w-96 h-96">
            <canvas id="servicesDistributionChart"></canvas>
        </span>
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
@endsection