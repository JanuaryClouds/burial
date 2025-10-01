@extends('layouts.stisla.guest')
@section('content')
    <title>CSWDO Burial Assistance</title>
    <div
        class="container d-flex min-vh-100 align-items-center justify-content-center p-5"
    >
        <div class="row w-100">
            <div class="col-12 col-lg-10 mx-auto">
                <form action="{{ route('guest.burial-assistance.store') }}" method="post" enctype="multipart/form-data" id="burialAssistanceForm">
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
                                        <p class="fw-bold">Upon submission, the application will be sent to the CSWDO of Taguig City for review. After of which, information you provided will be permanent and cannot be edited or deleted. However, you are allowed to request a change of claimants of the assistance after a short review. You can use the system's tracker to check the status of your request</p>
                                        <button class="btn btn-outline-primary" id="autofillBtn">
                                            <i class="fas fa-wand-magic-sparkles"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col mt-4">
                            <x-deceased-form />
                        </div>
                        <div class="col mt-2">
                            <x-claimant-form />
                        </div>
                        <div class="col mt-2">
                            <x-burial-assistance-details-form />
                        </div>
                        <div class="col mt-2">
                            <x-burial-assistance-image-requirements :readonly="false" />
                        </div>
                        <div
                            class="col mt-4"
                        >
                            <div
                                class="container bg-white rounded p-4 shadow"
                            >
                                <p class="">
                                    By submitting this form, you agree to providing CSDWO of Taguig City the information required for the burial assistance. Please ensure the information provided is accurate and complete. Failure to comply will result into the rejection of your request.
                                </p>
                                <div class="d-flex justify-content-center">
                                    <!-- Button trigger modal -->
                                    <button
                                        type="button"
                                        class="btn btn-primary mr-2"
                                        data-toggle="modal"
                                        data-target="#confirmationModal"
                                    >
                                        <i class="fas fa-paper-plane"></i>
                                        Submit
                                    </button>
                                    <a
                                        name=""
                                        id=""
                                        class="btn btn-secondary"
                                        href="{{ route('landing.page') }}"
                                        role="button"
                                        ><i class="fa-solid fa-times mr-1"></i>Cancel</a
                                    >
                                    
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
                                                        data-dismiss="modal"
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
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        const religion = document.getElementById('religion');
        const muslimRequirements = document.getElementById('muslim-requirements');
        religion.addEventListener('change', function () {
            if (religion.value == 2) {
                muslimRequirements.classList.remove('d-none');
                document.getElementById('burialRites').setAttribute('required', 'required');
                document.getElementById('internmentCertificate').setAttribute('required', 'required');
            } else {
                muslimRequirements.classList.add('d-none');
                document.getElementById('burialRites').value = '';
                document.getElementById('internmentCertificate').value = '';
                document.getElementById('burialRites').removeAttribute('required');
                document.getElementById('internmentCertificate').removeAttribute('required');
            }
        })
    </script>
@endsection
