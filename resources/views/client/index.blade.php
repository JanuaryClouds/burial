@extends('layouts.metronic.admin')
<title>{{ $page_title }}</title>
@section('content')
    @can('view-clients')
        @include('admin.partials.cards')
    @endcan
    <div class="card">
        <div class="card-body">
            @include('partials.datatable.index', [
                'data' => $data,
                'columns' => $columns,
            ])
        </div>
    </div>
@endsection
