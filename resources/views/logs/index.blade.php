@extends('layouts.metronic.admin')
<title>Activity</title>
@section('content')
<div class="row">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title fs-2">Activity</h2>
        </div>
        <div class="card-body">
            <x-activity-logs-table />
        </div>
    </div>
    <!-- TODO: remove hasRole -->
    @if (auth()->user()->hasRole('superadmin'))
        <div class="card mt-8">
            <div class="card-header">
                <h2 class="card-title fs-2">Process Logs</h2>
            </div>
            <div class="card-body">
                <x-process-logs-table />
            </div>
        </div>
    @endif
</div>
@endsection