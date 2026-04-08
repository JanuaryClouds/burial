@extends('layouts.metronic.admin')
<title>Activity</title>
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title fs-2">Activity</h2>
            </div>
            <div class="card-body">
                @can('view-logs')
                    @include('partials.datatable.index', [
                        'columns' => $columns,
                        'data' => $data,
                        'route' => route('activity.logs'),
                    ])
                @else
                    <p class="card-text">You do not have permission to view activity logs</p>
                @endcan
            </div>
        </div>
    </div>
@endsection
