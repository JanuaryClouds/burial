@extends('layouts.metronic.admin')
<title>{{ $page_title }}</title>
@section('content')
    @include('admin.partials.cards')
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
