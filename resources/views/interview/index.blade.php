@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Interviews</h4>
        </div>
        <div class="card-body">
            @include('partials.datatable.index', [
                'columns' => $columns,
                'data' => $data,
            ])
        </div>
    </div>
@endsection
