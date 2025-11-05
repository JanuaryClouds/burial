@extends('layouts.metronic.guest')
@php
    if (!app()->isLocal()) {
        return redirect()->route('landing.page')->with('error', 'You are not allowed to access this page.');
    }
@endphp
<title>Component tests</title>
@section('content')
    <div class="container min-vh-100 d-flex justify-content-center align-items-center">
        <div class="row my-auto">
            <div class="col">
                <x-general-intake-sheet />
            </div>
        </div>
    </div>
@endsection