@extends('layouts.metronic.guest')
@section('content')
    <div class="d-flex flex-column flex-center min-vh-100" style="margin-top: -10rem; z-index: -1;">
        <img src="{{ asset('images/CSWDO.webp') }}" alt="CSWDO Logo" class="mb-5" style="width: 150px; height: auto;">
        <h1 class="display-4 text-center">Funeral Assistance System</h1>
        <h2 class="display-6 text-center">City Social Welfare & Development Office</h2>
        <h3 class="text-cemter">Taguig City</h3>
    </div>

    <div class="bg-body py-10">
        <div class="container-xxl gap-20">
            <div class="d-flex flex-column gap-20">
                @include('guest.partial.services')
                <div class="separator"></div>
                @include('admin.partial.cards')
                <div class="separator"></div>
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-6">
                        @include('guest.partial.process')
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        @include('guest.partial.documents')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
