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
                    <p class="card-text">This page and this form is created for testing purposes. The intent was to set the
                        TLC
                        Portal link to be a form containing hidden inputs for a key and the citizen's uuid</p>
                    <div class="row g-1">
                        @foreach ($testUsers as $user)
                            @php
                                // DONE: handle in backend instead of blade template
                                // DONE: send to group chat as reference, as well as the sso and endpoint
                                // DONE: add a non-existing user link for testing
                                // DONE: use development version of production from hostinger
                                // DONE: use the TLC portal notification API endpoint when creating notifications
                                // DONE: update the API endpoint of TLC portal for user info fetching
                                // DONE: create or edit an endpoint that allows the portal to check the session of this system
                                // DONE: when citizen accesses the system and clicks the funeral assistance card, send an API call or expose an endpoint telling the portal that the citizen has a session in funeral assistance system
                                // DONE: when the citizen logs out, send an API call or expose an endpoint telling the portal that the citizen has logged out of funeral assistance system AND the portal
                                // DONE: add a hashed signature to the logout endpoint, same as the SSO process
                                // DONE: notification panel might not be necessary as notifications are handled by the portal
                            @endphp
                            <div class="col-4 d-flex">
                                <a href="{{ $user['url'] }}" class="btn btn-secondary w-100">
                                    {{ $user['name'] }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
