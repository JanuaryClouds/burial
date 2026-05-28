@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column gap-4">
        @role('staff')
            <div class="card multicolor-border">
                <div class="card-header">
                    <h5 class="card-title">All Interviews</h5>
                </div>
                <div class="card-body">
                    @include('partials.datatable.index', [
                        'columns' => $allDataColumns,
                        'data' => $allData,
                        'src' => 'allData',
                    ])
                </div>
            </div>
        @endrole
        <div class="card multicolor-border">
            <div class="card-header">
                <h5 class="card-title">My Interviews</h5>
            </div>
            <div class="card-body">
                @include('partials.datatable.index', [
                    'columns' => $personalDataColumns,
                    'data' => $personalData,
                    'src' => 'personalData',
                ])
            </div>
        </div>
    </div>
@endsection
