@props([
    'readonly' => false,
    'files' => []
])

@php
    if (app()->isLocal()) {
        $readonly = false;
    }
@endphp

<div class="bg-white shadow-sm rounded p-4">
    <h2>Image Requirements</h2>
    <div class="row flex-column justify-content-center align-items-center g-2">
    @if (count($files) > 0)
        @foreach ($files as $file)
            @if (!str_contains($file['name'], 'cheque-proof'))
                <div class="col mb-2">
                    <section class="section">
                        <div class="section-title">
                            <h3>{{ Str::replace('_', ' ',Str::title(Str::substr($file['name'], 0, -4))) }}</h3>
                        </div>
                        @if(Str::startsWith($file['mime'], 'image/'))
                            <img src="data:{{ $file['mime'] }};base64,{{ base64_encode($file['content']) }}" 
                                alt="{{ $file['name'] }}" class="w-100">
                        @endif
                    </section>
                </div>
            @endif
        @endforeach
    @elseif (count($files) == 0 && (!auth()->user() || !auth()->user()->isAdmin()))
        <div
            class="col mb-4"
        >
            <x-form-image-submission
                name="images[death_certificate]"
                label="Certified True Copy of Registered Death Certificate"
                helpText="From Taguig City Civil Registry."
                required="{{ $readonly }}"
            />
        </div>
        <div
            class="col mb-4"
        >
            <x-form-image-submission
                name="images[funeral_contract]"
                label="Certified True Copy of Funeral Contract"
                helpText="From Funeral Establishment."
                required="{{ $readonly }}"
            />
        </div>
        <div class="col mb-4">
            <x-form-image-submission
                name="images[claimant_valid_id]"
                label="Photocopy of Valid Identification Card of Claimant"
                required="{{ $readonly }}"
            />
        </div>
        <div class="col mb-4">
            <x-form-image-submission
                name="images[deceased_valid_id]"
                label="Photocopy of Valid Identification Card of Deceased"
                required="{{ $readonly }}"
            />
        </div>
        <hr>
        <div id="muslim-requirements" class="col mb-4 p-0">
            <h2 class="text-center">For Muslim Citizens</h2>
            <div
                class="col mb-4"
            >
                <x-form-image-submission
                    name="images[burial_rites]"
                    label="Certificate of Burial Rites (signed by IMAM)"
                    helpText="From Muslim/Islam Religious Community"
                    id="burialRites"
                />
            </div>
            <div
                class="col"
            >
                <x-form-image-submission
                    name="images[internment_certificate]"
                    label="Certificate of Internment"
                    helpText="From Muslim/Islam Religious Community"
                    id="internmentCertificate"
                />
            </div>
        </div>
        <hr>
        <div
            class="col mb-4"
        >
            <x-form-image-submission
                name="images[proof_of_relationship]"
                label="Proof of Relationship between Claimant and Deceased"
                helpText="From Taguig City Civil Registry or Philippine Statistics Authority (PSA)."
                required="{{ $readonly }}"
            />
        </div>
        <div class="col mb-4">
            <ul class="list-group">
                <li class="list-group-item active">Example of documents for Proof of Relationship</li>
                <li class="list-group-item">Marriage Contract (Spouse) - From Taguig City Civil Registry</li>
                <li class="list-group-item">Birth Certificate - From Taguig City Civil Registry</li>
                <li class="list-group-item">Baptismal Certificate (for Siblings, Children, Parents) - Church where the claimant is baptized</li>
            </ul>
        </div>
    @endif
    </div>
</div>
