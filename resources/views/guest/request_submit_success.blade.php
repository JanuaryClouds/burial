@use('SimpleSoftwareIO\QrCode\Facades\QrCode')
@extends('layouts.guest')
@section('content')
<title>Successfully Submited Request</title>
    <div class="container align-items-center justify-content-center">
        <img src="{{ asset('images/CSWDO.webp') }}" alt="" class="img-fluid w-36 mx-auto">
        <h1 class="text-center">Request Submitted Successfully</h1>
        <p class="text-center">Your request will be processed. Please ensure the contact details you provided are active.</p>
        <p class="text-center fw-semibold">Use the QR code below to copy the code to your request or click the button to copy.</p>

        @php
            $serviceRequestUuid = session('service');
        @endphp

        <div class="d-flex justify-content-center">
            <div class="card">
                <div class="card-header mx-auto bg-white">
                    @if ($serviceRequestUuid)
                        {!! QrCode::format('svg')->size(250)->generate($serviceRequestUuid) !!}
                    @endif
                </div>
                <div class="card-body text-center">
                    <!-- Button trigger modal -->
                    <button
                        type="button"
                        class="btn btn-outline-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#reditectMenu"
                        onclick="copyToClipboard('{{ $serviceRequestUuid }}')"
                    >
                        Copy Tracking Code
                    </button>

                    <script>
                        function copyToClipboard(text) {
                            navigator.clipboard.writeText(text).then(function() {
                            }, function(err) {
                                console.error('Could not copy text: ', err);
                            });
                        }
                    </script>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div
            class="modal fade"
            id="reditectMenu"
            tabindex="-1"
            role="dialog"
            aria-labelledby="modalTitleId"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Success!
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">The code has been copied to your clipboard. You can track your request from the landing page.</div>
                    </div>
                    <div class="modal-footer">
                        <a
                            name=""
                            id=""
                            class="btn btn-primary"
                            href="/"
                            role="button"
                            >Home</a
                        >
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            var modalId = document.getElementById('reditectMenu');
        
            modalId.addEventListener('show.bs.modal', function (event) {
                  // Button that triggered the modal
                  let button = event.relatedTarget;
                  // Extract info from data-bs-* attributes
                  let recipient = button.getAttribute('data-bs-whatever');
        
                // Use above variables to manipulate the DOM
            });
        </script>
        
    </div>
@endsection