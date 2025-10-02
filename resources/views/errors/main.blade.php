@extends('layouts.stisla.guest')
<title></title>
@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100 min-vw-100">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">You have encountered an error</h5>
            <p class="card-text">Seems like you encountered an error doing your action.</p>
        </div>
        <div class="card-footer">
            @if (auth()->user() && auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                    Go Back
                </a>
            @elseif (auth()->user() && auth()->user()->hasRole('superadmin'))
                <a href="{{ route('superadmin.dashboard') }}" class="btn btn-primary">
                    Go Back
                </a>
            @else
                <a href="{{ route('landing.page') }}" class="btn btn-primary">
                    Go Back
                </a>
            @endif
        </div>
    </div>
</div>
@endsection