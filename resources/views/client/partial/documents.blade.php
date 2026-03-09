@props([
    'readonly' => false,
    'files' => [],
])

@php
    if (app()->isLocal()) {
        $readonly = false;
    }
@endphp

<h5 class="card-title">SUBMITTED DOCUMENTS</h5>
@if (Request::routeIs('general.intake.form'))
    <p><strong>Optionally</strong>, you can upload the following documents to shorten the processing time during the
        interview:</p>
@endif
<div class="row flex-column justify-content-center align-items-center g-2">
    @if (count($files) > 0)
        @foreach ($files as $file)
            @if (!str_contains($file['name'], 'cheque-proof'))
                <div class="col mb-2">
                    <section class="section">
                        <div class="section-title">
                            <h3>{{ Str::replace('_', ' ', Str::title(Str::substr($file['name'], 0, -8))) }}</h3>
                        </div>
                        <img src="{{ route('clients.documents', [$client->tracking_no, $file['name']]) }}"
                            class="w-100" />
                    </section>
                </div>
            @endif
        @endforeach
    @elseif (count($files) == 0 && auth()->user())
        <div class="col mb-4">
            <x-form-image-submission name="images[death_certificate]"
                label="Certified True Copy of Registered Death Certificate" helpText="From Taguig City Civil Registry."
                required="{{ $readonly }}" />
        </div>
        <div class="col mb-4">
            <x-form-image-submission name="images[funeral_contract]" label="Certified True Copy of Funeral Contract"
                helpText="From Funeral Establishment." required="{{ $readonly }}" />
        </div>
        <div class="col mb-4">
            <x-form-image-submission name="images[claimant_valid_id]"
                label="Photocopy of Valid Identification Card of Claimant" required="{{ $readonly }}" />
        </div>
        <div class="col mb-4">
            <x-form-image-submission name="images[deceased_valid_id]"
                label="Photocopy of Valid Identification Card of Deceased" required="{{ $readonly }}" />
        </div>
        <div id="muslim-requirements" class="col mb-4 p-0">
            <hr>
            <h2 class="text-center">For Muslim Citizens</h2>
            <div class="col mb-4">
                <x-form-image-submission name="images[burial_rites]"
                    label="Certificate of Burial Rites (signed by IMAM)"
                    helpText="From Muslim/Islam Religious Community" id="burialRites" />
            </div>
            <div class="col">
                <x-form-image-submission name="images[internment_certificate]" label="Certificate of Internment"
                    helpText="From Muslim/Islam Religious Community" id="internmentCertificate" />
            </div>
        </div>
    @endif
</div>
