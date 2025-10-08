@extends('layouts.metronic.guest')
<title>CSWDO Burial Assistance</title>
@section('content')
<div >

</div>


    <div class="row d-flex justify-content-center h-100 w-100 mx-0 d-flex flex-column align-items-center">
        <div class="col-lg-5 col-md-8 col-sm-8 col-12">
            <div class="row d-flex flex-column bg-white rounded shadow-sm p-4">
                <div class="col d-flex flex-column justify-content-center align-items-center">
                    <img class="w-25 mx-auto" src="{{ asset('images/CSWDO.webp') }}" alt="">
                    <h4 class="text-center fw-semibold text-black">CSWDO Burial Assistance</h4>
                </div>
                <div class="col d-flex flex-column mt-4 align-items-center">
                    <button class="btn btn-primary col-6" type="button" data-bs-toggle="modal"
                        data-bs-target="#request-modal">Request Burial Assistance</button>
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
                <div class="col">
                    <p class="text-center">If you have already requested a burial assistance, please use the tracker below</p>
                </div>
                <div class="col d-flex flex-column align-items-center">
                    <button class="btn btn-secondary col-6 btn-sm" type="button" data-bs-toggle="modal"
                        data-bs-target="#tracker-modal">Track Application</button>
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
                <div class="col">
                    <p class="text-center">For CSWDO Employees, log in</p>
                </div>
                <div class="col d-flex flex-column">
                    <a href="{{ route('login.page') }}" role="button" class="btn btn-link">
                        Manage Burial Assistances
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection