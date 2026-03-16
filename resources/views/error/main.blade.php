@extends('layouts.metronic.guest')
@section('content')
    <div class="container d-flex justify-content-center align-items-center min-vh-100 min-vw-100">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">You have encountered an error</h5>
                <p class="card-text">Seems like you encountered an error doing your action.</p>
            </div>
            <div class="card-footer">
                <a href="{{ url()->previous() == url()->current() ? route('home') : (parse_url(url()->previous(), PHP_URL_HOST) === request()->getHost() ? url()->previous() : route('home')) }}"
                    class="btn btn-primary">
                    Go Back
                </a>
            </div>
        </div>
    </div>
@endsection
