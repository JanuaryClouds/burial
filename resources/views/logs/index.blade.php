@extends('layouts.metronic.admin')
<title>Activity</title>
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title fs-2">Activity</h2>
            </div>
            <div class="card-body">
                @include('partials.datatable.index', [
                    'columns' => $columns,
                ])
            </div>
        </div>
    </div>
@endsection
