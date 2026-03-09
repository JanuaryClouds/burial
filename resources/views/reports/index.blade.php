@extends('layouts.metronic.admin')
<title>Reports - {{ $model }}</title>
@section('content')
    <div class="mb-8">
        <x-filter-data-form type="{{ $model }}" :startDate="$startDate" :endDate="$endDate" />
    </div>
    @include('admin.partial.cards')
    <div class="row mt-8">
        @includeWhen(Route::is('reports.clients'), 'reports.partial.client-charts')
        @includeWhen(Route::is('reports.funerals'), 'reports.partial.funeral-charts')
        @includeWhen(Route::is('reports.burial-assistances'), 'reports.partial.burial-charts')
        @includeWhen(Route::is('reports.deceased'), 'reports.partial.deceased-charts')
        @includeWhen(Route::is('reports.claimants'), 'reports.partial.claimants-charts')
        @includeWhen(Route::is('reports.cheques'), 'reports.partial.cheques-charts')
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
        <x-export-to-pdf-button :startDate="$startDate" :endDate="$endDate" type="{{ $model }}" />
    </div>
@endsection
