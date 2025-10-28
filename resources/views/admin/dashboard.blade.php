@extends('layouts.stisla.admin')
@section('content')
<title>Dashboard</title>
<div class="main-content">
    <section class="section">
        <div class="jumbotron p-0"
            style="background: url('{{ asset('images/cover.webp') }}') no-repeat center center / cover;"
        >
            <div style="background-color: rgba(0, 0, 0, 0.75);" class="px-5 py-5 rounded text-white">
                <h1 class="display-6">Welcome, {{ Auth::user()->first_name }}!</h1>
                <p class="lead text-muted">Where do you want to go first?</p>
                <hr class="my-4 bg-white">
                <a href="{{ route('admin.applications.manage', ['id' => $lastLogs->last()->burialAssistance->id]) }}" class="btn btn-primary mr-2">
                    <i class="fas fa-clock-rotate-left me-2"></i> Continue Last Application
                </a>
                <a href="{{ route('reports.burial-assistances') }}" class="btn btn-outline-light mr-2">
                    <i class="fas fa-chart-line me-2"></i> Report on Assistances
                </a>
                <!-- TODO: link to logs -->
                <a href="#" class="btn btn-outline-light mr-2">
                    <i class="fas fa-clipboard-list me-2"></i> View Logs
                </a>
            </div>
        </div>
    </section>
    <section class="section">
        <div class="section-body">
            <x-admin-dashboard-cards :cardData="$cardData" />
        </div>
    </section>
    <section class="section">
        <div class="section-title">For You</div>
        <div class="section-body">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                    <x-assigned-applications-list :applications="$assignedApplications" />
                </div>
                <!-- <div class="col-12 col-md-12 col-lg-4">
                    <x-recent-activity :lastLogs="$lastLogs" />
                </div> -->
            </div>
        </div>
    </section>
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <x-latest-applications-table />
                </div>
            </div>        
        </div>
    </section>
    <section class="section">
        <div class="section-title">Charts</div>
        <div class="section-body">
            <div class="row">
                <x-application-barangay-chart />
                <x-application-status-charts />
            </div>
        </div>
    </section>
    <!-- <section class="section">
        <div class="section-title">
            Tables
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h4>Burial Assistance Applications per Barangay</h4>
                        </div>
                        <div class="card-body">
                            <x-per-barangay-table :applications="$applicationsByBarangay" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->
</div>

@endsection