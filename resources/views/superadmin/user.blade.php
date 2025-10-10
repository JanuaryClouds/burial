@extends('layouts.stisla.superadmin')
<title>Manage {{ $user->first_name }} {{ $user->last_name }}</title>
@section('content')
<div class="main-content">
    <div class="d-flex flex-column">
        <section class="section">
            <div class="section-header">
                <h1>Manage {{ $user->first_name }} {{ $user->last_name }}</h1>
            </div>
        </section>
        <div class="row">
            <div class="col col-6 col-md-6 col-lg-6">
                @include('components.user-active-form', ['user' => $user])
            </div>
            <div class="col col-6 col-md-6 col-lg-6">
                @include('components.last-login-card', ['user' => $user])
            </div>
        </div>
        @include(
            'components.user-route-restrictions', [
                'user' => $user,
                'routes' => $routes,
                'restrictions' => $restrictions,
            ]
        )
    </div>
</div>
@endsection