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
        {{-- TODO: create charts for cheques --}}
        <div class="col-12 mt-8">
            @includeWhen(Route::is('reports.clients'), 'reports.partial.client-datatable')
            @includeWhen(Route::is('reports.funerals'), 'reports.partial.funeral-datatable')
            @includeWhen(Route::is('reports.burial-assistances'), 'reports.partial.burial-datatable')
            @includeWhen(Route::is('reports.deceased'), 'reports.partial.deceased-datatable')
            @includeWhen(Route::is('reports.claimants'), 'reports.partial.claimants-datatable')
            {{-- TODO: create datatables for cheques --}}
        </div>
    </div>
    <div class="d-flex justify-content-center mt-8">
        <x-export-to-pdf-button :startDate="$startDate" :endDate="$endDate" type="{{ $model }}" />
    </div>
@endsection
