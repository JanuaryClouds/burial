@extends('layouts.metronic.guest')
@section('content')
    @php
        if (!app()->environment('local', 'testing')) {
            abort(404);
        }
    @endphp
    <title>Ground Zero</title>
    <div class="container-xxl min-vh-100">
        <div class="d-flex flex-column gap-4 my-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Ground Zero Form</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning fs-4" role="alert">
                        <strong>Notice</strong> This page is used for testing purposes. This page is not going to be
                        accessible from the production environment.
                    </div>

                    <p class="card-text">This page and this form is created for testing purposes. The intent was to set the
                        TLC
                        Portal link to be a form containing hidden inputs for a key and the citizen's uuid</p>
                    <div class="row g-1">
                        @foreach ($testUsers as $user)
                            <div class="col-4 d-flex">
                                <a href="{{ $user['url'] }}" class="btn btn-secondary w-100">
                                    {{ $user['label'] }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
