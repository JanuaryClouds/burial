@extends('layouts.stisla.superadmin')
<title>Dashboard</title>
@section('content')
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
                    <a href="{{ route('superadmin.assignments') }}" class="btn btn-light mr-2">
                        <i class="fas fa-check-to-slot me-2"></i> Manage Assignments
                    </a>
                    <div class="dropdown">
                        <button id="reports-dropdown" class="btn btn-light dropdown-toggle mr-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                    <!-- TODO: link to logs -->
                    <a href="{{ route('activity.logs') }}" class="btn btn-light mr-2">
                        <i class="fas fa-clipboard-list me-2"></i> View Logs
                    </a>
                    <a href="{{ route('superadmin.cms.users') }}" class="btn btn-light">
                        <i class="fas fa-users me-2"></i> Manage Accounts
                    </a>
                </div>
            </div>
        </div>
    </section>
    <div class="section-body">
        <x-admin-dashboard-cards :cardData="$cardData" />
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <x-latest-applications-table />
            </div>
        </div>
    </div>
    <section>
        <div class="section-body">
            <div class="row">
                <x-application-status-charts />
                <x-application-barangay-chart />
            </div>
        </div>
    </section>
    <x-applications-modal-loader />
</div>
@endsection