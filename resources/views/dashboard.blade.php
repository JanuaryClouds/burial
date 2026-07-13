@extends('layouts.app')
@section('content')
    <title>Dashboard</title>
    <div class="d-flex flex-column gap-6">
        @role('staff')
            @include('user.partials.quick-links')
        @endrole
        <div class="row">
            <div class="col-12 d-flex flex-column gap-6 mb-6">
                @unlessrole('staff')
                    @include('user.partials.quick-links')
                @endunlessrole
                @role('staff')
                    <div class="card multicolor-border">
                        <div class="card-header">
                            <h4 class="card-title">
                                Recent Applications
                            </h4>
                        </div>
                        <div class="card-body">
                            @include('partials.datatable.index', [
                                'src' => 'data',
                                'columns' => $columns,
                            ])
                        </div>
                    </div>
                @endrole
            </div>
        </div>
    </div>
@endsection
