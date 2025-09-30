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
                <!-- TODO: Monthly requests -->
                <div class="col-12 col-lg-6" x-data="{ period: 'month'}">
                    <div class="card">
                        <div class="card-header">
                            <h4>Burial Assistance Applications per Barangay</h4>
                            <span class="card-header-action">
                                <select name="" id="" class="form-control" x-on:change="period = $event.target.value">
                                    <option value="year">Year</option>
                                    <option value="month">Month</option>
                                    <option value="week">Week</option>
                                    <option value="today">Today</option>
                                </select>
                            </span>
                        </div>
                        <div class="card-body">
                            <div
                                x-show="period == 'year'"
                                x-cloak
                                x-transition
                            >
                                <canvas 
                                    id="applications-year"
                                    data-chart-data='@json($applicationsThisYear->pluck('count'))'
                                    data-chart-labels='@json($applicationsThisYear->pluck('name'))'
                                    data-chart-type="bar"
                                ></canvas>
                            </div>
                            <div
                                x-show="period == 'month'"
                                x-cloak
                                x-transition
                            >
                                <canvas 
                                    id="applications-month"
                                    data-chart-data='@json($applicationsThisMonth->pluck('count'))'
                                    data-chart-labels='@json($applicationsThisMonth->pluck('name'))'
                                    data-chart-type="bar"
                                ></canvas>
                            </div>
                            <div
                                x-show="period == 'week'"
                                x-cloak
                                x-transition
                            >
                                <canvas 
                                    id="applications-week"
                                    data-chart-data='@json($applicationsThisWeek->pluck('count'))'
                                    data-chart-labels='@json($applicationsThisWeek->pluck('name'))'
                                    data-chart-type="bar"
                                ></canvas>
                            </div>
                            <div
                                x-show="period == 'today'"
                                x-cloak
                                x-transition
                            >
                                <canvas 
                                    id="application-today"
                                    data-chart-data='@json($applicationsThisDay->pluck('count'))'
                                    data-chart-labels='@json($applicationsThisDay->pluck('name'))'
                                    data-chart-type="bar"
                                ></canvas>
                            </div>
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