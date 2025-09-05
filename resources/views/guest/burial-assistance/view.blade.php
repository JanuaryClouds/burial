@extends('layouts.guest')
@section('content')
    <title>CSWDO Burial Assistance</title>
    <div
        class="container d-flex min-vh-100 align-items-center justify-content-center m-5"
    >
        <div class="row w-100">
            <div class="col-12 col-lg-8 mx-auto">
                <form action="{{ route('guest.burial-assistance.store') }}" method="post">
                    @csrf
                    <div
                        class="row d-flex flex-column justify-content-center align-items-center g-2 gap-4"
                    >
                        <div class="col">
                            <div
                                class="container bg-white rounded p-4 shadow"
                            >
                                <div
                                    class="row flex-column justify-content-start align-items-end g-2"
                                >
                                    <div class="col">
                                        <h1>Burial Assistance Application Form</h1>
                                    </div>
                                    <div class="col">
                                        <p>Please fill out the following information. Fields marked with an asterisk are required. Leave blank if inapplicable.</p>
                                        <p class="fw-bold">Upon submission, the application will be sent to the CSWDO of Taguig City for review. After of which, information you provided will be permanent and cannot be edited or deleted.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <x-deceased-form />
                        </div>
                        <div class="col">
                            <x-claimant-form />
                        </div>
                        <div class="col">
                            <x-burial-assistance-details-form />
                        </div>
                        <div class="col">
                            <x-burial-assistance-image-requirements />
                        </div>
                        <div
                            class="col"
                        >
                            <div
                                class="container bg-white rounded p-4 shadow"
                            >
                                <p class="">
                                    By submitting this form, you agree to providing CSDWO of Taguig City the information required for the burial assistance. Please ensure the information provided is accurate and complete. Failure to comply will result into the rejection of your request.
                                </p>
                                <div class="d-flex justify-content-center gap-2">
                                    <a
                                        name=""
                                        id=""
                                        class="btn btn-secondary"
                                        href="{{ route('landing.page') }}"
                                        role="button"
                                        >Cancel</a
                                    >
                                    <!-- Button trigger modal -->
                                    <button
                                        type="button"
                                        class="btn btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmationModal"
                                    >
                                        Submit
                                    </button>
                                    
                                    <!-- Modal -->
                                    <div
                                        class="modal fade"
                                        id="confirmationModal"
                                        tabindex="-1"
                                        role="dialog"
                                        aria-labelledby="modalTitleId"
                                        aria-hidden="true"
                                    >
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalTitleId">
                                                        Confirm Application for Burial Assistance
                                                    </h5>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="container-fluid">
                                                        <p class="">
                                                            Please double check and confirm the information you have provided. Ensure the provided claimant mobile number is active. The system will send a tracking code for this application via SMS using the provided mobile number.
                                                        </p>
                                                        <p class="fw-bold">
                                                            False information may result into the rejection of this application.
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button
                                                        type="button"
                                                        class="btn btn-secondary"
                                                        data-bs-dismiss="modal"
                                                    >
                                                        Close
                                                    </button>
                                                    <button
                                                        type="submit"
                                                        name=""
                                                        id=""
                                                        class="btn btn-primary"
                                                    >
                                                        Confirm Submission
                                                    </button>
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
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
@endsection
