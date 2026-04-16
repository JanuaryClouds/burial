@extends('layouts.metronic.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ $page_title }}</h4>
        </div>
        <div class="card-body">
            @include('partials.datatable.index', [
                'data' => $data,
                'classes' => 'with-status with-actions',
                'columns' => $columns,
            ])
        </div>
    </div>
    @can('create-reports')
        @if (isset($data) && $data->count() > 0)
            <div class="d-flex justify-content-center mt-10">
                <a name="" id="" class="btn btn-primary align-self-end" data-no-loader
                    href="{{ route('applications.export.all') }}" role="button">Export Database to Excel</a>
            </div>
        @endif
    @endcan
@endsection
