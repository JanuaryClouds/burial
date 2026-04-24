@extends('layouts.metronic.admin')
@section('content')
    <div class="d-flex flex-column gap-4">
        <div class="row">
            <div class="col-6 col-lg-3">
                <livewire:counter :model="'App\Models\BurialAssistance'" :label="'Total Burial Assistance Records'" :iconName="'abstract-29'" :iconPathsCount="2"
                    :scope="'Total'" />
            </div>
            <div class="col-6 col-lg-3">
                <livewire:counter :model="'App\Models\BurialAssistance'" :label="'Pending Burial Assistances'" :iconName="'watch'" :iconPathsCount="2"
                    :scope="'Pending'" />
            </div>
            <div class="col-6 col-lg-3">
                <livewire:counter :model="'App\Models\BurialAssistance'" :label="'Processing Burial Assistances'" :iconName="'timer'" :iconPathsCount="3"
                    :scope="'Processing'" />
            </div>
            <div class="col-6 col-lg-3">
                <livewire:counter :model="'App\Models\BurialAssistance'" :label="'Released Burial Assistances'" :iconName="'check-circle'" :iconPathsCount="2"
                    :scope="'Released'" />
            </div>
        </div>
        <div class="card">
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
    </div>
@endsection
