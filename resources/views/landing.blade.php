@extends('layouts.metronic.guest')
@section('content')
    <div class="d-flex flex-column flex-center min-vh-100" style="margin-top: -10rem; z-index: -1;">
        <img src="{{ asset('images/CSWDO.webp') }}" alt="CSWDO Logo" class="mb-5" style="width: 150px; height: auto;">
        <h1 class="display-4">Funeral Assistance System</h1>
        <h2 class="display-6">City Social Welfare & Development Office</h2>
        <h3>Taguig City</h3>
    </div>

    <div class="bg-white py-10">
        <div class="container-xxl gap-20">
            <div class="d-flex flex-column gap-20">
                @include('guest.partial.services')
                <div class="separator"></div>
                @include('admin.partial.cards')
                <div class="separator"></div>
                <div class="row">
                    <div class="col">
                        @include('guest.partial.process')
                    </div>
                    <div class="col">
                        @include('guest.partial.documents')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
