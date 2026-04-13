@extends('layouts.metronic.admin')
<title>{{ $page_title }}</title>
@section('content')
    @include('admin.partials.cards')
    <div class="card mt-8">
        <div class="card-header">
            <h2 class="card-title fs-2 fw-medium">Manage {{ $resource }}</h2>
        </div>
        <div class="card-body">
            @can('view-clients')
                @if (!isset($data) || $data->count() == 0)
                    <p class="text-muted text-center">
                        No Data.
                    </p>
                @else
                    @include('partials.datatable.index', [
                        'data' => $data,
                        'resource' => $resource,
                        'columns' => $columns,
                    ])
                @endif
            @else
                <p class="card-text">
                    You do not have permission to view this resource.
                </p>
            @endcan
        </div>
    </div>
@endsection
