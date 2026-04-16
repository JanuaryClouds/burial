@extends('layouts.metronic.admin')
<title>{{ $page_title }}</title>
@section('content')
    @can('view-clients')
        @include('admin.partials.cards')
    @endcan
    <div class="card mt-8">
        <div class="card-header">
            <h2 class="card-title fs-2 fw-medium">Manage Clients</h2>
        </div>
        <div class="card-body">
            @include('partials.datatable.index', [
                'data' => $data,
                'columns' => $columns,
            ])
        </div>
    </div>
@endsection
