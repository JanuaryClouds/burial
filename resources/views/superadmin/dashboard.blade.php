@extends('layouts.stisla.superadmin')
<title>Dashboard</title>
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Dashboard</h1>
        </div>
    </section>
    <section>
        <div class="section-body">
            <x-admin-dashboard-cards :cardData="$cardData" />
        </div>
    </section>
    <section>
        <div class="section-body">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-4">
                    <x-recent-activity :lastLogs="$lastLogs" />
                </div>
                <div class="col-12 col-md-12 col-lg-8">
                    <x-pending-applications-list :pendingApplications="$pendingApplications" />
                </div>
            </div>
        </div>
    </section>
    <section class="section">
        <div class="section-title">
            Charts and Tables
        </div>
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
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Burial Assistance Per Barangay</h4>
                        </div>
                        <div class="card-body">
                            <x-per-barangay-table :applications="$applicationsByBarangay" />
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <x-encoders-table :encoders="$applicationsByAdmin" />
                </div>
            </div>
        </div>
    </section>
</div>
@endsection