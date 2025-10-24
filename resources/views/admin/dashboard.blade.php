@extends('layouts.stisla.admin')
@section('content')
<title>Dashboard</title>
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Dashboard</h1>
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
                <div class="col-12 col-md-12 col-lg-8">
                    <x-assigned-applications-list :applications="$assignedApplications" />
                </div>
                <div class="col-12 col-md-12 col-lg-4">
                    <x-recent-activity :lastLogs="$lastLogs" />
                </div>
            </div>
        </div>
    </section>
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <x-pending-applications-list :pendingApplications="$pendingApplications" />
                </div>
            </div>        
        </div>
    </section>
    <section class="section">
        <div class="section-title">Charts</div>
        <div class="section-body">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Burial Assistance Applications per Barangay</h4>
                        </div>
                        <div class="card-body">
                            <canvas 
                                id="applicationsDistributions"
                                data-chart-data='@json($perBarangay->pluck('count'))'
                                data-chart-labels='@json($perBarangay->pluck('name'))'
                                data-chart-type="pie"
                            ></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 ">
                    <div class="card">
                        <div class="card-header">
                            <h4>Your Activity Summary</h4>
                        </div>
                        <div class="card-body">
                            <canvas 
                                id="montlyActivityGraph"
                                data-chart-data='@json($monthlyActivity->pluck('count'))'
                                data-chart-labels='@json($monthlyActivity->pluck('week'))'
                                data-chart-type="line"
                            ></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section">
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
    </section>
</div>

@endsection