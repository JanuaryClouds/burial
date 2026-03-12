@extends('layouts.metronic.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Beneficiaries</h4>
        </div>
        <div class="card-body">
            @include('partials.datatable.index', [
                'columns' => $columns,
                'data' => $data,
            ])
        </div>
    </div>
@endsection
