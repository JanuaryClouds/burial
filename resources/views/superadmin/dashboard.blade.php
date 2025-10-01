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
</div>
@endsection