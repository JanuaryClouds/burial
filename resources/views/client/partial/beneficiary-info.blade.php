@props([
    'readonly' => false,
    'client',
])
@php
    if (isset($client)) {
        $beneficiary = $client->beneficiary;
    }
@endphp
<h5 class="card-title">II. BENEFICIARY'S IDENTIFYING INFORMATION</h5>
    <div class="row">
        <div class="col-12 col-md-8 col-lg-3">
            <x-form-input 
                name="ben_first_name"
                label="1.1. Beneficiary's First Name"
                required="true"
                type="text"
                value="{{ $beneficiary->first_name ?? null }}"
                :readonly="$readonly"
            />
        </div>
        <div class="col-12 col-md-8 col-lg-3">
            <x-form-input 
                name="ben_middle_name"
                label="1.2. Beneficiary's Middle Name"
                type="text"
                value="{{ $beneficiary->middle_name ?? null }}"
                :readonly="$readonly"
            />
        </div>
        <div class="col-12 col-md-8 col-lg-3">
            <x-form-input 
                name="ben_last_name"
                label="1.3. Beneficiary's Last Name"
                required="true"
                type="text"
                value="{{ $beneficiary->last_name ?? null }}"
                :readonly="$readonly"
            />
        </div>
        <div class="col-12 col-md-8 col-lg-3">
            <x-form-input 
                name="ben_suffix"
                label="1.4. Beneficiary's Suffix"
                type="text"
                value="{{ $beneficiary->suffix ?? null }}"
                :readonly="$readonly"
            />
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-4 col-lg-2">
            <x-form-select
                name="ben_sex_id"
                label="2. Sex"
                required="true"
                :options="$beneficiary->sex ?? $genders"
                :selected="$beneficiary->sex->id ?? ''"
                :disabled="$readonly"
            />
        </div>
        <div class="col-12 col-md-6 col-lg-2">
            <x-form-input
                name="ben_date_of_birth"
                label="3. Date of Birth"
                required="true"
                type="date"
                value="{{ $beneficiary->date_of_birth ?? null }}"
                :readonly="$readonly"
            />
        </div>
        <div class="col-12 col-md-6 col-lg-2">
            <x-form-input
                name="ben_date_of_death"
                label="Date of Death"
                required="true"
                type="date"
                value="{{ $beneficiary->date_of_death ?? null }}"
                :readonly="$readonly"
            />
        </div>
        <div class="col-12 col-md-4 col-lg-4">
            <x-form-select
                name="ben_religion_id"
                label="Religion"
                required="true"
                :options="$beneficiary->religion ?? $religions"
                :selected="$beneficiary->religion->id ?? ''"
                :disabled="$readonly"
                />
            </div>
        </div>
    <div class="row">
        <div class="col-12 col-md-6 col-lg-4">
            <x-form-input
                name="ben_place_of_birth"
                label="4.1. Place of Birth"
                required="true"
                type="text"
                value="{{ $beneficiary->place_of_birth ?? null }}"
                :readonly="$readonly"
            />
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <x-form-select
                name="ben_barangay_id"
                label="4.2. Barangay"
                required="true"
                :options="$beneficiary->barangay ?? $barangays"
                :selected="$beneficiary->barangay->id ?? ''"
                :disabled="$readonly"
            />
        </div>
    </div>