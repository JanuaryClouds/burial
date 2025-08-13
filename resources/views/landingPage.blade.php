@extends('layouts.guest')
@section('content')
<title>CSWDO Burial Assistance</title>
<div 
    class="container d-flex min-vh-100 min-vw-100 align-items-center justify-content-center"
>
    <div
        class="row flex-column justify-content-center align-items-center g-2 bg-white p-4 rounded shadow mx-auto w-50 w-md-75 w-lg-50"
    >
        <div class="col d-flex flex-column container">
            <img class="w-25 mx-auto" src="{{ asset('images/CSWDO.webp') }}" alt="" >
            <h3 class="text-center fw-semibold">Welcome to CSWDO Burial Assistance</h3>
        </div>
        <div class="col d-flex flex-column gap-2">
            <a
                name=""
                id=""
                class="btn btn-primary w-50 mx-auto"
                href="{{ route('guest.burial.request') }}"
                role="button"
                >Request Burial Assistance</a
            >
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
                            <form action="{{ route('guest.request.tracker', 'uuid') }}" method="post">
                                @csrf
                                @method('POST')
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
                                        <div class="col">Track your request by providing the request's code below</div>
                                        <div class="col">
                                            <input
                                            type="text"
                                                class="form-control"
                                                name="uuid"
                                                id="uuid"
                                                aria-describedby="helpId"
                                                placeholder=""
                                                />
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