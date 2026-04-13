@extends('layouts.metronic.admin')
@section('content')
    <title>Dashboard</title>
    @include('admin.partials.cards')
    <div class="row mt-5 mt-xl-8">
        <div class="col-12">
            @can('view-clients')
                @include('client.partials.latest-table', [
                    'data' => $data,
                    'columns' => $columns,
                ])
            @else
                <div class="alert alert-info">
                    You do not have permission to view client data.
                </div>
            @endcan
        </div>
    </div>
@endsection
