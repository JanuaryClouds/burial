@props(['claimant', 'readonly' => false, 'disabled' => false, 'claimant_change' => false])

<h2>Claimant Information</h2>
<p>Please fill out the following information. Fields marked with an asterisk are required. Leave blank if inapplicable.
</p>

@if (!$claimant_change)
    <div class="row justify-content-start align-items-center">
        <div class="col-12 col-md-4 col-lg-6">
            <x-form-input name="first_name" id="first_name" label="First Name" required="true"
                value="{{ $claimant->first_name ?? '' }}" :readonly="$readonly" :disabled="$disabled" />
        </div>
        <div class="col-12 col-md-4 col-lg-2">
            <x-form-input name="middle_name" id="middle_name" label="Middle Name"
                value="{{ $claimant->middle_name ?? '' }}" :readonly="$readonly" :disabled="$disabled" />
        </div>
        <div class="col-6 col-lg-2">
            <x-form-input name="last_name" id="last_name" label="Last Name" required="true"
                value="{{ $claimant->last_name ?? '' }}" :readonly="$readonly" :disabled="$disabled" />
        </div>
        <div class="col-6 col-lg-2">
            <x-form-input name="suffix" id="suffix" label="Suffix" value="{{ $claimant->suffix ?? '' }}"
                :readonly="$readonly" :disabled="$disabled" />
        </div>
    </div>
@endif
<div class="row justify-content-start align-items-center">
    @if ($claimant_change)
        <div class="col-12 col-lg-4">
            <x-form-input name="email" id="email" label="Email Address of New Claimant" required="true"
                type="email" value="{{ $claimant->email ?? '' }}" :readonly="false" :autocomplete="false" />
        </div>
    @endif
    <div class="col-12 col-lg-3">
        <x-form-input name="date_of_birth" id="date_of_birth" label="Date of Birth" required="true" type="date"
            value="{{ $claimant->date_of_birth ?? '' }}" :readonly="$readonly" :disabled="$disabled" />
    </div>
    <div class="col-12 col-lg-4">
        <x-form-select name="relationship_id" id="relationship" label="Relationship of the Deceased to the Claimant"
            required="true" :options="$relationships" :selected="$claimant->relationship_to_deceased ?? ''" :disabled="$readonly" />
    </div>
    @if (!$claimant_change)
        <div class="col-12 col-lg-3">
            <x-form-input name="contact_number" id="contact_number" label="Mobile Number" required="true" maxlength="11"
                value="{{ $claimant->contact_number ?? '' }}" :readonly="$readonly" :disabled="$disabled" />
        </div>
    @endif
</div>
<div class="row justify-content-start align-items-center g-2">
    <div class="col-12 col-lg-6">
        <x-form-input name="address" id="address" label="Address" required="true"
            value="{{ $claimant->address ?? '' }}" :readonly="$readonly" :disabled="$disabled" />
    </div>
    <div class="col-12 col-lg-4">
        <x-form-select name="barangay_id" id="barangay_id" label="Barangay" required="true" :options="$barangays"
            :selected="$claimant->barangay_id ?? ''" :disabled="$readonly" />
    </div>
    <div class="col-12 col-lg-2">
        <x-form-input name="city" id="city" label="City" required="true"
            value="{{ $claimant->city ?? 'Taguig City' }}" :readonly="true" />
    </div>
</div>
