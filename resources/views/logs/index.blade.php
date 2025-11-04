@extends('layouts.stisla.admin')
<title>Activity</title>
@section('content')
<div class="main-content">
    <div class="section">
        <div class="section-header">
            <h1>Activity</h1>
        </div>
    </div>
    <div>
        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>Activity</h4>
                </div>
                <div class="card-body">
                    <x-activity-logs-table />
                </div>
            </div>
            @if (auth()->user()->hasRole('superadmin'))
                <div class="card">
                    <div class="card-header">
                        <h4>Process Logs</h4>
                    </div>
                    <div class="card-body">
                        <x-process-logs-table />
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection