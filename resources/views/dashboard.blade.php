@extends('layouts.app')
@section('content')
    <title>Dashboard</title>
    <div class="row g-6">
        @can('view-clients')
            <div class="col">
                @include('admin.partials.cards')
            </div>
        @endcan
        @cannot('view-clients')
            <div class="col-12 col-lg-8">
                @include('user.partials.quick-links')
            </div>
            <div class="col-12 col-lg-4">
                <livewire:notification.index lazy />
            </div>
        @endcannot
        @can('view-clients')
            <div class="col-12">
                @include('client.partials.latest-table', [
                    'data' => $data,
                    'columns' => $columns,
                ])
            </div>
        @endcan
    </div>
@endsection
