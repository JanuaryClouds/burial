@extends('layouts.metronic.guest')
<title>{{ $burialAssistance->deceased->first_name }} {{ $burialAssistance->deceased->last_name }} Burial Assistance
</title>
@section('content')
    <div class="container-xxl min-vh-100 my-10">
        <div class="d-flex flex-column gap-4">
            <form action="{{ route('burial.claimant-change.store', ['uuid' => $burialAssistance->id]) }}" method="post"
                id="claimantChangeForm">
                @csrf
                <div class="card">
                    <div class="card-body">
                        @include('client.partial.client-info')
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('client.partial.documents')
                    </div>
                </div>
            </form>
        </div>

        <div class="modal fade" id="confirmChangeClaimantModal" tabindex="-1" data-bs-backdrop="static"
            data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Confirm Inputted Data
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="fs-4">
                            Please ensure you gave correct information. Incorrect or invalid information will result in the
                            rejection of changing claimants for this assistance.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="button" class="btn btn-success" id="submitButton">
                            Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const confirmClaimantChangeButton = document.getElementById('confirmClaimantChangeButton');
            const claimantChangeForm = document.getElementById('claimantChangeForm');
            const submitButton = document.getElementById('submitButton');
            const requiredFields = claimantChangeForm.querySelectorAll('[required]');
            checkRequiredFields();

            function checkRequiredFields() {
                const allFilled = [...requiredFields].every(field => field.value.trim() !== '');
                confirmClaimantChangeButton.disabled = !allFilled;
            }

            requiredFields.forEach((field) => {
                field.addEventListener('input', checkRequiredFields);
                field.addEventListener('blur', () => {
                    if (field.value.trim() === '') {
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });
            });

            submitButton.addEventListener('click', () => {
                claimantChangeForm.submit();
            });
        })
    </script>
@endsection
