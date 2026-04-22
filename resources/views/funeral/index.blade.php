@extends('layouts.metronic.admin')
<title>{{ $page_title }}</title>
@section('content')
    <div class="d-flex flex-column gap-4">
        <div class="row">
            <div class="col-12 col-lg-4">
                <livewire:counter :model="'App\Models\FuneralAssistance'" :label="'Total Funeral Assistance Records'" :iconName="'abstract-29'" :iconPathsCount="2"
                    :scope="'total'" />
            </div>
            <div class="col-12 col-lg-4">
                <livewire:counter :model="'App\Models\FuneralAssistance'" :label="'Approved Funeral Assistances'" :iconName="'check-circle'" :iconPathsCount="2"
                    :scope="'approved'" />
            </div>
            <div class="col-12 col-lg-4">
                <livewire:counter :model="'App\Models\FuneralAssistance'" :label="'Forwarded to Cemetery Staff'" :iconName="'double-right'" :iconPathsCount="2"
                    :scope="'forwarded'" />
            </div>
        </div>
    </div>
    <div class="card mt-8">
        <div class="card-body">
            @include('partials.datatable.index', [
                'data' => $data,
                'columns' => $columns,
                'classes' => 'with-status with-actions',
            ])
        </div>
    </div>
@endsection
