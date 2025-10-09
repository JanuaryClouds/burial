@extends('layouts.metronic.guest')
@section('content')
    <title>CSWDO Burial Assistance</title>
    <div
        class="d-flex flex-column flex-lg-row flex-column-fluid stepper stepper-pills stepper-column stepper-multistep"
        id="kt_create_account_stepper"
        data-kt-stepper="true"
    >
        <!-- begin::Aside -->
        <div
            class="d-flex flex-column flex-lg-row-auto w-lg-350px w-xl-500px"
            style="background-color: #dc3545;"
        >
            <div
                class="d-flex flex-column position-lg-fixed top-0 bottom-0 w-lg-350px w-xl-500px scroll-y w-xl-500px scroll-y bgi-size-conver bgi-position-center"
                style="background-image: {{ asset('images/cover.webp') }};"
            >
                <!--begin:Header-->
                <div
                    class="d-flex flex-center py-10 py-lg-20 mt-lg-20"
                >
                    <a href="{{ route('landing.page') }}">
                        <img src="{{ asset('images/CSWDO.webp') }}" alt="" class="h-100px"
                            style="background-color: white; border-radius: 100%;"
                        >
                    </a>
                </div>
                <!--end:Header-->
                <!--begin:Body-->
                <div
                    class="d-flex flex-row-fluid justify-content-center p-10"
                >
                    <div class="stepper-nav">
                        <!-- step 1 -->
                        <div class="stepper-item current" data-kt-stepper-element="nav">
                            <div class="stepper-wrapper">
                                <div class="stepper-icon rounded-3">
                                    <i class="ki-duotone ki-check fs-2 stepper-check"></i>
                                    <span class="stepper-number">1</span>
                                </div>
                                <div class="stepper-label">
                                    <h3 class="stepper-title fs-2">Disclaimer</h3>
                                    <div class="stepper-desc fw-normal">
                                        Before you proceed, please understand this notice.
                                    </div>
                                </div>
                            </div>
                            <div class="stepper-line h-20px"></div>
                        </div>
                        <!-- step 2 -->
                        <div class="stepper-item" data-kt-stepper-element="nav">
                            <div class="stepper-wrapper">
                                <div class="stepper-icon rounded-3">
                                    <i class="ki-duotone ki-check fs-2 stepper-check"></i>
                                    <span class="stepper-number">2</span>
                                </div>
                                <div class="stepper-label">
                                    <h3 class="stepper-title fs-2">Deceased Person's Information</h3>
                                    <div class="stepper-desc fw-normal">
                                        Provide information regarding the deceased person.
                                    </div>
                                </div>
                            </div>
                            <div class="stepper-line h-20px"></div>
                        </div>
                        <!-- step 3 -->
                        <div class="stepper-item" data-kt-stepper-element="nav">
                            <div class="stepper-wrapper">
                                <div class="stepper-icon rounded-3">
                                    <i class="ki-duotone ki-check fs-2 stepper-check"></i>
                                    <span class="stepper-number">3</span>
                                </div>
                                <div class="stepper-label">
                                    <h3 class="stepper-title fs-2">Claimant Information</h3>
                                    <div class="stepper-desc fw-normal">
                                        Beneficiary of Burial Assistance.
                                    </div>
                                </div>
                            </div>
                            <div class="stepper-line h-20px"></div>
                        </div>
                        <!-- step 4 -->
                        <div class="stepper-item" data-kt-stepper-element="nav">
                            <div class="stepper-wrapper">
                                <div class="stepper-icon rounded-3">
                                    <i class="ki-duotone ki-check fs-2 stepper-check"></i>
                                    <span class="stepper-number">4</span>
                                </div>
                                <div class="stepper-label">
                                    <h3 class="stepper-title fs-2">Burial Assistance Details</h3>
                                    <div class="stepper-desc fw-normal">
                                        Funeraria, amount, and remarks.
                                    </div>
                                </div>
                            </div>
                            <div class="stepper-line h-20px"></div>
                        </div>
                        <!-- step 5 -->
                        <div class="stepper-item" data-kt-stepper-element="nav">
                            <div class="stepper-wrapper">
                                <div class="stepper-icon rounded-3">
                                    <i class="ki-duotone ki-check fs-2 stepper-check"></i>
                                    <span class="stepper-number">5</span>
                                </div>
                                <div class="stepper-label">
                                    <h3 class="stepper-title fs-2">Image Requirements</h3>
                                    <div class="stepper-desc fw-normal">
                                        Provide photos of the required documents.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end:Body -->
                <!-- begin:Footer -->
                <div class="d-flex flex-center flex-wrap px-5 py-10">
                    <div class="d-flex fw-normal">
                        <a href="{{ route('landing.page') }}" class="text-white fw-bold">Home</a>
                    </div>
                </div>
                <!-- end:Footer -->
            </div>
        </div>
        <!-- End::Aside -->

        <!-- begin::Body -->
        <div class="d-flex flex-column flex-lg-row-fluid">
            <!-- begin::Content -->
            <div class="d-flex flex-center flex-column flex-column-fluid bg-white">
                <!-- begin::Wrapper -->
                <div class="w-lg-650px w-xl-700px p-10 p-lg-15 mx-auto">

                    <!-- begin::Form -->
                    <form
                        action="{{ route('guest.burial-assistance.store') }}"
                        method="post"
                        enctype="multipart/form-data"
                        id="kt_create_account_form"
                        class="my-auto pb-5 fv-plugins-bootstrap5 fv-plugins-framework"
                        novalidate="novalidate"
                    >
                        <!-- Step 1: Disclaimer -->
                        <div data-kt-stepper-element="content" class="current">
                            @csrf
                            <div class="bg-white rounded p-4 shadow">
                                <h1>Burial Assistance Application Form</h1>
                                <p>Please fill out the following information. Fields marked with an asterisk are required. Leave blank if inapplicable.</p>
                                <p class="fw-bold">
                                    Upon submission, the application will be sent to the CSWDO of Taguig City for review. After which, the information you provided will be permanent and cannot be edited or deleted. You are allowed to request a change of claimants after review using the system tracker.
                                </p>

                                @if (app()->isLocal())
                                    <button class="btn btn-outline-primary" id="autofillBtn">
                                        <i class="fas fa-wand-magic-sparkles"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <!-- Step 2: Deceased Information -->
                        <div data-kt-stepper-element="content">
                            @include('components.deceased-form')
                        </div>

                        <!-- Step 3: Claimant Information -->
                        <div data-kt-stepper-element="content">
                            @include('components.claimant-form')
                        </div>

                        <!-- Step 4: Burial Assistance Details -->
                        <div data-kt-stepper-element="content">
                            @include('components.burial-assistance-details-form')
                        </div>

                        <!-- Step 5: Image Requirements -->
                        <div data-kt-stepper-element="content">
                            @include('components.burial-assistance-image-requirements')
                        </div>

                        <!-- Stepper Actions -->
                        <div class="d-flex flex-stack pt-15">
                            <div class="me-2">
                                <button type="button" class="btn btn-lg btn-light-primary me-3" data-kt-stepper-action="previous">
                                    <i class="fas fa-arrow-left fs-4 me-1"></i>
                                    Previous
                                </button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-lg btn-primary" data-kt-stepper-action="next">
                                    Continue
                                    <i class="fas fa-arrow-right fs-4 ms-1"></i>
                                </button>
                                <button type="button" class="btn btn-lg btn-primary" data-kt-stepper-action="submit">
                                    <span class="indicator-label">
                                        Submit
                                        <i class="fas fa-upload fs-4 ms-2"></i>
                                    </span>
                                    <span class="indicator-progress">
                                        Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                    <!-- end::Form -->

                </div>
                <!-- end::Wrapper -->
            </div>
            <!-- end::Content -->
        </div>
        <!-- end::Body -->


        <!-- <div class="row w-100 bg-white">
            <div class="col-12 col-lg-10 mx-auto">
            </div>
        </div> -->
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
