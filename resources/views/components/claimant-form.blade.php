@props([
    'claimant',
    'readonly' => false,
    'disabled' => false
])

<div class="bg-white shadow-sm rounded p-4">
    <h2>Claimant Information</h2>
    <p>Please fill out the following information. Fields marked with an asterisk are required. Leave blank if inapplicable.</p>

    <div
        class="row justify-content-start align-items-center g-2"
    >
        <div class="col-6">
            <x-form-input
                name="claimant[first_name]"
                id="first_name"
                label="Given Name"
                required="true"
                value="{{ $claimant->first_name ?? '' }}"
                readonly="{{ $readonly }}"
                disabled="{{ $disabled }}"
            />
        </div>
        <div class="col-2">
            <x-form-input
                name="claimant[middle_name]"
                id="middle_name"
                label="Middle Name"
                value="{{ $claimant->middle_name ?? '' }}"
                readonly="{{ $readonly }}"
                disabled="{{ $disabled }}"
            />
        </div>
        <div class="col-2">
            <x-form-input
                name="claimant[last_name]"
                id="last_name"
                label="Last Name"
                required="true"
                value="{{ $claimant->last_name ?? '' }}"
                readonly="{{ $readonly }}"
                disabled="{{ $disabled }}"
            />
        </div>
        <div class="col-2">
            <x-form-input
                name="claimant[suffix]"
                id="suffix"
                label="Suffix"
                value="{{ $claimant->suffix ?? '' }}"
                readonly="{{ $readonly }}"
                disabled="{{ $disabled }}"
            />
        </div>
    </div>
    <div
        class="row justify-content-start align-items-center g-2"
    >
        <div class="col">
            <x-form-select
                name="claimant[relationship_to_deceased]"
                id="relationship"
                label="Relationship to the Deceased"
                required="true"
                :options="$relationships->pluck('name', 'id')"
                :selected="$claimant->relationship_to_deceased ?? ''"
                disabled="{{ $disabled }}"
            />
        </div>
        <div class="col">
            <x-form-input
                name="claimant[mobile_number]"
                id="mobile_number"
                label="Mobile Number"
                required="true"
                maxlength="11"
                value="{{ $claimant->mobile_number ?? '' }}"
                readonly="{{ $readonly }}"
                disabled="{{ $disabled }}"
            />
        </div>
    </div>
    <div
        class="row justify-content-start align-items-center g-2"
    >
        <div class="col-8">
            <x-form-input
                name="claimant[address]"
                id="address"
                label="Address"
                required="true"
                value="{{ $claimant->address ?? '' }}"
                readonly="{{ $readonly }}"
                disabled="{{ $disabled }}"
            />
        </div>
        <div class="col-4">
            <x-form-select
                name="claimant[barangay_id]"
                id="barangay_id"
                label="Barangay"
                required="true"
                :options="$barangays->pluck('name', 'id')"
                :selected="$claimant->barangay_id ?? ''"
                disabled="{{ $disabled }}"
            />
        </div>
    </div>
</div>
