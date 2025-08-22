@extends('layouts.superadmin')
<title>Dashboard</title>
@section('content')
<div class="container d-flex flex-column gap-4 g-0 p-0">
    <h1 class="g-0 bg-white p-3 rounded shadow-sm w-100 mb-0">Dashboard</h1>
    <div class="d-flex justify-content-start flex-wrap gap-4 p-0">
        <div class="card mb-3 rounded shadow-sm" style="max-width: 480px;" >
            <div class="row g-0">
                <div class="col-md-4 d-flex justify-content-center align-items-center rounded" style="background-color: chartreuse;">
                    <i class="fa-solid fa-asterisk" style="font-size: 50px;"></i>
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title">{{ $totalData }}</h5>
                        <p class="card-text text-muted">
                            Total number of data including Burial Assistance Requests, Service Providers, and Burials Serviced.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3 rounded shadow-sm" style="max-width: 480px;" >
            <div class="row g-0">
                <div class="col-md-4 d-flex justify-content-center align-items-center rounded" style="background-color: burlywood;">
                    <i class="fa-solid fa-users" style="font-size: 50px;"></i>
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title">{{ $totalUsers }}</h5>
                        <p class="card-text text-muted">
                            Total number of users saved in the system. Please double check if all accounts are used and none are excess
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3 rounded shadow-sm" style="min-width: 480px; max-width: 480px; max-height: 156px; min-height: 156px;" >
            <div class="row g-0">
                <div class="col-md-4 d-flex justify-content-center align-items-center rounded" style="background-color: cornflowerblue; min-height: 156px;">
                    <i class="fa-solid fa-bell-concierge" style="font-size: 50px;"></i>
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title">{{ $avgRequestsPerMonth }}</h5>
                        <p class="card-text text-muted">
                            Average requests per Month. To check how much traffic is occurring in the requests feature.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3 rounded shadow-sm" style="min-width: 480px; max-width: 480px; max-height: 156px; min-height: 156px;" >
            <div class="row g-0">
                <div class="col-md-4 d-flex justify-content-center align-items-center rounded" style="background-color: coral; min-height: 156px;">
                    <i class="fa-solid fa-magnifying-glass" style="font-size: 50px;"></i>
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title">{{ $avgTracksPerMonth }}</h5>
                        <p class="card-text text-muted">
                            How often requests are being tracked.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div
        class="container-fluid bg-white rounded shadow-sm p-3"
    >
        <h2 class="g-0">Calendar of Tracking Activity</h2>
        <div
            class="row d-flex flex-column justify-content-center align-items-center g-2"
        >
            <div class="col">
                <x-tracking-activity-calendar />
            </div>
        </div>
        
    </div>
    
</div>

@endsection