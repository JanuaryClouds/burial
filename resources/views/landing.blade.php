@extends('layouts.metronic.guest')
<title>CSWDO Burial Assistance</title>
@section('content')
    <div class="d-flex flex-column flex-lg-row min-h-lg-100 max-h-lg-100">
        <!-- begin::Aside -->
        <div
            class="d-flex flex-root flex-lg-column flex-lg-row-fluid flex-lg-column-fluid flex-lg-grow-auto flex-lg-center w-xl-25 gap-lg-10 p-2 p-lg-0">
            <!-- begin::header -->
            <div class="d-flex flex-center">
                <a href="{{ route('landing.page') }}">
                    <img src="{{ asset('images/CSWDO.webp') }}" alt="" class="h-lg-250px h-25px">
                </a>
            </div>
            <!-- end::header -->

            <!-- begin::Body -->
            <div class="d-flex flex-row flex-lg-column flex-center gap-2">

                <h1 class=" mb-8 fs-2hx d-none d-lg-block text-uppercase">City Social Welfare & Development Office</h1>
                <h2 class=" mb-0 fs-2hx">Funeral Assistance System</h2>
            </div>
            <!-- end::Body -->
        </div>
        <!-- end::Aside -->

        <!-- begin::Body -->
        <div class="d-flex flex-column">
            <!-- begin::Content -->
            <div class="d-flex flex-column flex-column-fluid">
                <!-- begin::Wrapper -->
                <div class="w-xl-500px h-100 d-flex flex-center justify-content-center">
                    <div class="row flex-column gap-5 bg-white shadow-sm rounded px-20 py-12">
                        <div class="col d-flex flex-center flex-column w-100">
                            @if (session()->has('citizen'))
                                <p>Welcome {{ session('citizen')['firstname'] }} {{ session('citizen')['lastname'] }}</p>
                            @endif
                            <p class="text-muted">How would you want to use the system?</p>
                        </div>
                        <div class="col d-flex flex-center w-100">
                            <a href="{{ route('general.intake.form') }}" class="btn btn-primary w-100 btn-lg">Fill out
                                Assistance Form</a>
                        </div>
                        <div class="col d-flex flex-center">
                            <button class="btn btn-secondary w-100 btn-lg" type="button" data-bs-toggle="modal"
                                data-bs-target="#tracker-modal">
                                Track Application
                            </button>
                            <div id="tracker-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('guest.burial-assistance.tracker') }}" method="post">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="container-fluid">
                                                    <div class="row d-flex flex-column justify-content-center g-2">
                                                        <div class="col">Track your application by providing the
                                                            application's
                                                            code below and the last four digits of your phone number</div>
                                                        <div class="col d-flex flex-column gap-2 align-items-center">
                                                            <input class="form-control text-center mt-4" type="text"
                                                                name="tracking_code" id="tracking_code"
                                                                placeholder="XXXXXX">
                                                            <input class="form-control text-center mt-2" type="text"
                                                                name="mobile_number" id="tracking_code" placeholder="XXXX">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-magnifying-glass"></i>
                                                    Track
                                                </button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="fas fa-times"></i>
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center my-5">
                            <div class="border-bottom border-gray-300 mw-50 w-75"></div>
                            <span class="text-gray-400 fs-7 mx-2" style="white-space: nowrap;">
                                <a href="{{ route('login.page') }}">
                                    Sign in
                                </a>
                            </span>
                            <div class="border-bottom border-gray-300 mw-50 w-75"></div>
                        </div>
                    </div>
                    <!-- end::Wrapper -->
                </div>
                <!-- end::Content -->
            </div>
            <!-- end::Body -->
        </div>
    </div>
@endsection