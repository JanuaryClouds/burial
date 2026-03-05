@extends('layouts.metronic.admin')
<title>{{ $page_title }}</title>
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ $page_title }}</h4>
        </div>
        <div class="card-body">
            @if (!isset($data) || $data->count() == 0)
                <p class="text-muted text-center">No Data</p>
            @else
                @include('partials.datatable.index', [
                    'classes' => 'with-status with-actions',
                    'columns' => $columns,
                    'route' => route('burial.index', ['status' => $status]),
                ])
            @endif
        </div>
    </div>
    @if (isset($data) && $data->count() > 0)
        <div class="d-flex justify-content-center mt-10">
            <a name="" id="" class="btn btn-primary align-self-end" data-no-loader
                href="{{ route('applications.export.all') }}" role="button">Export Database to Excel</a>
        </div>
    @endif
@endsection
