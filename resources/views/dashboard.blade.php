@extends('layouts.app')
@section('content')
    <title>Dashboard</title>
    <div class="d-flex flex-column gap-4">
        <div class="row">
            <div class="col-12 col-lg-8">
                @include('user.partials.quick-links')
            </div>
            <div class="col-12 col-lg-4">
                <livewire:notification.index lazy />
            </div>
        </div>
    </div>
@endsection
