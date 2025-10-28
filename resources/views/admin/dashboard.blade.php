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
                <div class="d-flex align-items-center justify-content-start">
                    <a href="{{ route('admin.applications.manage', ['id' => $lastLogs->last()->burialAssistance->id]) }}" class="btn btn-primary mr-2">
                        <i class="fas fa-clock-rotate-left me-2"></i> Continue Last Application
                    </a>
                    <div class="dropdown">
                        <button id="reports-dropdown" class="btn btn-outline-light dropdown-toggle mr-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-clipboard-list"></i>
                            Reports
                        </button>
                        <div class="dropdown-menu" aria-labelledby="reports-dropdown">
                            <a href="{{ route('reports.burial-assistances') }}" class="dropdown-item">Burial Assistances</a>
                            <a href="{{ route('reports.deceased') }}" class="dropdown-item">Deceased</a>
                            <a href="{{ route('reports.claimants') }}" class="dropdown-item">Claimants</a>
                            <a href="{{ route('reports.cheques') }}" class="dropdown-item">Cheques</a>
                        </div>
                    </div>
                    <a href="{{ route('admin.applications') }}" class="btn btn-outline-light mr-2">
                        <i class="fas fa-file-lines me-2"></i> Manage Applications
                    </a>
                    <!-- TODO: link to logs -->
                    <a href="#" class="btn btn-outline-light mr-2">
                        <i class="fas fa-clipboard-list me-2"></i> View Logs
                    </a>
                </div>
            </div>
        </div>
    </section>
    <div class="section-body">
        <x-admin-dashboard-cards :cardData="$cardData" />
    </div>
    <section class="section">
        <div class="section-title">For You</div>
    </section>
    <div class="section-body">
        <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
                <x-assigned-applications-list />
            </div>
            <!-- DEPRECATED -->
            <!-- <div class="col-12 col-md-12 col-lg-4">
                <x-recent-activity :lastLogs="$lastLogs" />
            </div> -->
        </div>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <x-latest-applications-table />
            </div>
        </div>        
    </div>
    <section class="section">
        <div class="section-title">Charts</div>
        <div class="section-body">
            <div class="row">
                <x-application-barangay-chart />
                <x-application-status-charts />
            </div>
        </div>
    </section>
    <!-- DEPRECATED -->
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