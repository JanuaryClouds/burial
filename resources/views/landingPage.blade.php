@extends('layouts.guest')
@section('content')
<title>CSWDO Burial Assistance</title>
<div 
    class="container d-flex min-vh-100 min-vw-100 align-items-center justify-content-center"
>
    <div
        class="row flex-column justify-content-center align-items-center g-2 bg-white p-4 rounded shadow mx-auto w-sm-100 w-md-75 w-50"
        style="@media screen {
            
        }"
    >
        <div class="col d-flex flex-column container">
            <img class="w-25 mx-auto" src="{{ asset('images/CSWDO.webp') }}" alt="" >
            <h3 class="text-center fw-semibold">CSWDO Burial Assistance</h3>
        </div>
        <div class="col d-flex flex-column gap-2">
            <!-- Button trigger modal -->
            <button
                type="button"
                class="btn btn-primary mx-auto w-50"
                data-bs-toggle="modal"
                data-bs-target="#requestModal"
            >
                Request Burial Assistance
            </button>
            
            <!-- Modal -->
            <div
                class="modal fade"
                id="requestModal"
                tabindex="-1"
                role="dialog"
                aria-labelledby="modalTitleId"
                aria-hidden="true"
            >
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTitleId">
                                Notice
                            </h5>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <p>Before proceeding with the request, please ensure you have the following requirements saved in image files:</p>
                                <ul class="list-group">
                                    <li class="list-group-item active">Required to All</li>
                                    <li class="list-group-item">Certified True Copy of Registered Death Certificate</li>
                                    <li class="list-group-item">Original Copy of Funeral Contract</li>
                                    <li class="list-group-item">Photocopy of Valid ID of Deceased and Claimant of the Burial Assistance</li>
                                    <li class="list-group-item">Proof of Relationship (such as Marriage Contract, Birth Certificate, and Baptismal Certificate)</li>
                                </ul>
                                <ul class="list-group mt-4">
                                    <li class="list-group-item active">For Muslim Citizens</li>
                                    <li class="list-group-item">Certificate of Burial Rites</li>
                                    <li class="list-group-item">Certificate of Internment</li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-end">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                            >
                                Close
                            </button>
                            <a
                                name=""
                                id=""
                                class="btn btn-primary"
                                href="{{ route('guest.burial-assistance.view') }}"
                                role="button"
                                >Proceed to Request</a
                            >
                        </div>
                    </div>
                </div>
            </div>
            
            <script>
                var modalId = document.getElementById('modalId');
            
                modalId.addEventListener('show.bs.modal', function (event) {
                      // Button that triggered the modal
                      let button = event.relatedTarget;
                      // Extract info from data-bs-* attributes
                      let recipient = button.getAttribute('data-bs-whatever');
            
                    // Use above variables to manipulate the DOM
                });
            </script>
            <p class="text-sm text-center text-gray-600">or use the tracker below if you have requested a burial assistance before</p>
            <div
                class="row d-flex flex-column justify-content-center align-items-center w-50 mx-auto mt-0 g-2"
            >
                <!-- Button trigger modal -->
                <button
                    type="button"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#trackerModal"
                >
                    Track Request
                </button>
                
                <!-- Modal -->
                <div
                    class="modal fade"
                    id="trackerModal"
                    tabindex="-1"
                    role="dialog"
                    aria-labelledby="modalTitleId"
                    aria-hidden="true"
                >
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <form action="{{ route('guest.burial-assistance.tracker') }}" method="post">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalTitleId">
                                        Track Request
                                    </h5>
                                    <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"
                                    ></button>
                                </div>
                                <div class="modal-body">
                                    <div class="container-fluid">
                                        <div
                                        class="row d-flex flex-column justify-content-center g-2"
                                        >
                                        <div class="col">Track your application by providing the application's code below and the last four digits of your phone number</div>
                                        <div class="col d-flex flex-column gap-2 align-items-center">
                                            <!-- <input
                                            type="text"
                                                class="form-control"
                                                name="uuid"
                                                id="uuid"
                                                aria-describedby="helpId"
                                                placeholder=""
                                                /> -->
                                                <input 
                                                    class="form-control text-center" 
                                                    type="text" 
                                                    name="tracking_code" 
                                                    id="tracking_code"
                                                    placeholder="XXXXXX"
                                                >
                                                <input 
                                                    class="form-control text-center"
                                                    type="text" 
                                                    name="mobile_number" 
                                                    id="tracking_code"
                                                    placeholder="XXXX"
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <div class="modal-footer">
                                        <button
                                        type="button"
                                        class="btn btn-secondary"
                                        data-bs-dismiss="modal"
                                        >
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary">Track</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <script>
                    var modalId = document.getElementById('trackerModal');
                
                    modalId.addEventListener('show.bs.modal', function (event) {
                          // Button that triggered the modal
                          let button = event.relatedTarget;
                          // Extract info from data-bs-* attributes
                          let recipient = button.getAttribute('data-bs-whatever');
                
                        // Use above variables to manipulate the DOM
                    });
                </script>
                
            </div>
        </div>
        <div class="col d-flex flex-column mt-4">
            <p class="text-sm text-center text-gray-600">For BAO Employees, access the manager below:</p>
            <a
                name=""
                id=""
                class="btn btn-outline-secondary w-50 mx-auto"
                href="{{ route('login.page') }}"
                role="button"
                >Manage Burial Services</a
            >
        </div>
    </div>
</div>
@endsection