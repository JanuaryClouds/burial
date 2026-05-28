@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column gap-4">
        @role('staff')
            <div class="card multicolor-border">
                <div class="card-header">
                    <h5 class="card-title">All Referrals</h5>
                </div>
                <div class="card-body">
                    @include('partials.datatable.index', [
                        'data' => $allData,
                        'columns' => $allDataColumns,
                        'src' => 'allData',
                    ])
                </div>
            </div>
        @endrole
        <div class="card multicolor-border">
            <div class="card-header">
                <h5 class="card-title">My Referrals</h5>
            </div>
            <div class="card-body">
                @include('partials.datatable.index', [
                    'data' => $personalData,
                    'columns' => $personalDataColumns,
                    'src' => 'personalData',
                ])
            </div>
        </div>
    </div>
@endsection
