@extends('layouts.metronic.admin')
<title>{{ $page_title }}</title>
@section('content')
    @include('admin.partials.cards')
    <div class="card mt-8">
        <div class="card-header">
            <h1 class="card-title">Manage Libreng Libing Applications</h1>
        </div>
        <div class="card-body">
            @if (!isset($data) || $data->count() == 0)
                <p class="text-muted text-center">No Applications</p>
            @else
                @include('partials.datatable.index', [
                    'data' => $data,
                    'resource' => $resource,
                    'columns' => $columns,
                    'classes' => 'with-status with-actions',
                ])
            @endif
        </div>
    </div>
@endsection
