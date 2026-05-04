@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column gap-4">
        <div class="card">
            <div class="card-body">
                @include('partials.datatable.index', [
                    'data' => $data,
                    'columns' => $columns,
                ])
            </div>
        </div>
    </div>
@endsection
