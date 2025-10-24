@extends('layouts.metronic.guest')
<title>CSWDO Burial Assistance</title>
@section('content')
<div
    class="d-flex flex-column flex-lg-row min-h-lg-100 max-h-lg-100"
>
    <!-- begin::Aside -->
    <div 
        class="d-flex flex-md-row flex-lg-column flex-lg-grow-auto flex-lg-center w-100 w-lg-350px w-xl-500px gap-4 gap-lg-20 p-2 p-lg-0" 
        style="background-color: #dc3545;"
    >
        <!-- begin::header -->
        <div class="d-flex flex-center">
            <a href="{{ route('landing.page') }}">
                <img src="{{ asset('images/CSWDO.webp') }}" alt="" class="h-lg-200px h-25px"
                    style="background-color: white; border-radius: 100%;"
                >
            </a>
        </div>
        <!-- end::header -->

        <!-- begin::Body -->
        <div class="d-flex flex-row flex-lg-column flex-center gap-1">
            <h1 class="text-white mb-9 fs-2hx d-none d-lg-block text-uppercase">Taguig City</h1>
            <h2 class="text-white d-none d-lg-block text-center">City Social Welfare & Development Office</h2>
            <h2 class="text-white mb-0">Burial Assistance System</h2>
        </div>
        <!-- end::Body -->

        <!-- start::footer -->
        <div class="d-flex flex-center">
            <a href="{{ route('login.page') }}" class="btn btn-light">
                Login as CSWDO
            </a>
        </div>
        <!-- end::footer -->
    </div>
    <!-- end::Aside -->

    <!-- begin::Body -->
    <div 
        class="d-flex flex-column flex-lg-row-fluid"
    >
        <!-- begin::Content -->
        <div class="d-flex flex-column flex-column-fluid">
            <!-- begin::Wrapper -->
            <div class="w-lg-650px w-xl-700px h-100 p-10 p-lg-15 mx-auto d-flex flex-center">
                <div class="row flex-column gap-5 bg-white shadow-sm rounded px-20 py-12">
                    <div class="col d-flex flex-center w-100">
                        <p class="text-muted">How do you want to use the system?</p>
                    </div>
                    <div class="col d-flex flex-center w-100">
                        <button class="btn btn-primary w-100 btn-lg" type="button" data-bs-toggle="modal"
                            data-bs-target="#request-modal">
                            Request Burial Assistance
                        </button>
                        <div id="request-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <div class="container">
                                            <p>Before proceeding with the request, please ensure you have the following
                                                requirements saved in image files:</p>
                                            <ul class="list-group">
                                                <li class="list-group-item active">Required to All</li>
                                                <li class="list-group-item">Certified True Copy of Registered Death
                                                    Certificate</li>
                                                <li class="list-group-item">Original Copy of Funeral Contract</li>
                                                <li class="list-group-item">Photocopy of Valid ID of Deceased and Claimant
                                                    of the Burial Assistance</li>
                                                <li class="list-group-item">Proof of Relationship (such as Marriage
                                                    Contract, Birth Certificate, and Baptismal Certificate)</li>
                                            </ul>
                                            <ul class="list-group mt-4">
                                                <li class="list-group-item active">For Muslim Citizens</li>
                                                <li class="list-group-item">Certificate of Burial Rites</li>
                                                <li class="list-group-item">Certificate of Internment</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <a name="" id="" class="btn btn-primary"
                                            href="{{ route('guest.burial-assistance.view') }}" role="button">Proceed to
                                            Request</a>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                                                    <div class="col">Track your application by providing the application's
                                                        code below and the last four digits of your phone number</div>
                                                    <div class="col d-flex flex-column gap-2 align-items-center">
                                                        <input class="form-control text-center mt-4" type="text"
                                                            name="tracking_code" id="tracking_code" placeholder="XXXXXX">
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
                </div>
            </div>
            <!-- end::Wrapper -->
        </div>
        <!-- end::Content -->
    </div>
    <!-- end::Body -->
</div>
@endsection