@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-body">
            @include('partials.datatable.index', [
                'columns' => $columns,
                'data' => $data,
            ])
        </div>
    </div>
@endsection
