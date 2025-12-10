@use('SimpleSoftwareIO\QrCode\Facades\QrCode')
@extends('layouts.guest')
@section('content')
@php
    $serviceRequestUuid = session('service');
@endphp

<title>Successfully Submited Request</title>
    <div class="container d-flex flex-column min-vh-100 align-items-center justify-content-center" x-cloak x-tranition">
        <div
            class="row d-flex flex-column justify-content-center align-items-center g-2 bg-white p-4 rounded shadow mx-auto w-50 w-md-75 w-lg-50"
        >
            <div class="col d-flex flex-column">
                <img src="{{ asset('images/CSWDO.webp') }}" alt="" class="img-fluid mx-auto" style="width: 100px">
                <h2 class="text-center">Request Submitted Successfully</h2>
                <p class="text-center text-body">Your request will be processed. Please ensure the contact details you provided are active.</p>
                <p class="text-center fw-semibold text-danger">Use the QR code below to copy the code to your request or click the button to copy.<br>Do NOT share your code to anyone who are not affiliated with the request or burial.</p>
            </div>
            <div class="col d-flex justify-content-center">
                <div class="card">
                    <div class="card-header mx-auto bg-white">
                        @if ($serviceRequestUuid)
                            {!! QrCode::format('svg')->size(150)->generate($serviceRequestUuid) !!}
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