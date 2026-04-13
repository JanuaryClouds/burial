@extends('layouts.metronic.admin')
<title>Reports - {{ $model }}</title>
@section('content')
    <div class="mb-8">
        @can('create-reports')
            @include('reports.partials.filter', [
                'type' => $model,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ])
        @endcan
    </div>
    @include('admin.partials.cards')
    <div class="row mt-8">
        @includeWhen(Route::is('reports.clients'), 'reports.partials.client-charts')
        @includeWhen(Route::is('reports.funerals'), 'reports.partials.funeral-charts')
        @includeWhen(Route::is('reports.burial-assistances'), 'reports.partials.burial-charts')
        @includeWhen(Route::is('reports.claimants'), 'reports.partials.claimants-charts')
        @includeWhen(Route::is('reports.cheques'), 'reports.partials.cheques-charts')
        <div class="col-12 mt-8">
            <div class="card">
                <div class="card-body">
                    @include('partials.datatable.index', [
                        'columns' => $columns,
                    ])
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center mt-8">
        @include('reports.partials.export-to-pdf', [
            'type' => $model,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])
    </div>
@endsection
