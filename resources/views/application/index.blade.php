@extends('layouts.app')
@section('content')
    <div class="card multicolor-border">
        <div class="card-body">
            @include('partials.datatable.index', [
                'columns' => $columns,
                'src' => 'data',
            ])
        </div>
    </div>
@endsection
