@extends('layouts.app')
@section('content')
    <title>Dashboard</title>
    <div class="d-flex flex-column gap-6">
        @role('staff')
            @include('user.partials.quick-links')
        @endrole
        <div class="row">
            <div class="col-12 col-lg-8 d-flex flex-column gap-6 mb-6">
                @unlessrole('staff')
                    @include('user.partials.quick-links')
                @endunlessrole
                @role('staff')
                    <div class="card multicolor-border">
                        <div class="card-body">
                            @include('partials.datatable.index', [
                                'columns' => $columns,
                                'data' => $data,
                                'src' => 'clients',
                            ])
                        </div>
                    </div>
                @endrole
            </div>
            <div class="col-12 col-lg-4 d-flex flex-column gap-6">
                <livewire:notification.index lazy />
            </div>
        </div>
    </div>
@endsection
