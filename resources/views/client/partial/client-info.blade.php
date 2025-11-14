@props([
    'readonly' => false,
    'client',
])
@php
    $header = 'Client Information';
    if (isset($client)) {
        $socialInfo = $client?->socialInfo;
        $demographic = $client?->demographic;
    }
@endphp
<h5 class="card-title">I. CLIENT'S IDENTIFYING INFORMATION</h5>
    <div class="d-flex flex-column gap-4">
        <div class="row">
            <div class="col-12 col-md-12 col-lg-4">
                <x-form-input 
                    name="first_name"
                    label="1.1 Client's First Name"
                    required="true"
                    value="{{ $client->first_name ?? null }}"
                    :readonly="$readonly"
                />
            </div>
            <div class="col-12 col-md-12 col-lg-3">
                <x-form-input 
                    name="middle_name"
                    label="1.2 Client's Middle Name"
                    value="{{ $client->middle_name ?? null }}"
                    :readonly="$readonly"
                />
            </div>
            <div class="col-12 col-md-12 col-lg-3">
                <x-form-input 
                    name="last_name"
                    label="1.3 Client's Last Name"
                    required="true"
                    value="{{ $client->last_name ?? null }}"
                    :readonly="$readonly"
                />
            </div>
            <div class="col-12 col-md-12 col-lg-2">
                <x-form-input 
                    name="suffix"
                    label="1.4 Client's Suffix"
                    value="{{ $client->suffix ?? null }}"
                    :readonly="$readonly"
                />
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-md-6 col-lg-2">
                <x-form-input 
                    name="age"
                    label="2. Age"
                    required="true"
                    type="number"
                    value="{{ $client->age ?? null }}"
                    :readonly="$readonly"
                />
            </div>
            <div class="col-6 col-md-6 col-lg-2">
                <x-form-select 
                    name="sex_id"
                    label="3. Sex"
                    required="true"
                    :selected="$demographic->sex->id ?? ''"
                    :options="$genders"
                    disabled="{{ $readonly }}"
                />
            </div>
            <div class="col-12 col-md-3 col-lg-2">
                <x-form-input
                    name="date_of_birth"
                    label="4. Date of Birth"
                    required="true"
                    type="date"
                    value="{{ $client->date_of_birth ?? null }}"
                    :readonly="$readonly"
                />
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-12 col-md-12 col-lg-2">
                <x-form-input
                    name="house_no"
                    label="5.1. House Number"
                    required="true"
                    type="text"
                    value="{{ $client->house_no ?? null }}"
                    :readonly="$readonly"
                />
            </div>
            <div class="col-12 col-md-12 col-lg-4">
                <x-form-input
                    name="street"
                    label="5.2. Street"
                    required="true"
                    type="text"
                    value="{{ $client->street ?? null }}"
                    :readonly="$readonly"
                />
            </div>
            <div class="col-12 col-md-12 col-lg-3">
                <x-form-select
                    name="barangay_id"
                    label="5.3 Barangay"
                    required="true"
                    :options="$barangays"
                    :selected="$client->barangay->id ?? ''"
                    :disabled="$readonly"
                />
            </div>
            <div class="col-12 col-md-12 col-lg-2">
                <x-form-select
                    name="district_id"
                    label="5.4. District"
                    required="true"
                    :options="$districts"
                    :selected="$client->district->id ?? ''"
                    :disabled="$readonly"
                />
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12 col-lg-4">
                <x-form-input
                    name="city"
                    label="5.5. City"
                    type="text"
                    value="Taguig City"
                    readonly="true"
                />
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-12 col-md-6 col-lg-6">
                <x-form-select
                    name="relationship_id"
                    label="6. Relationship to Beneficiary"
                    required="true"
                    :options="$relationships"
                    :selected="$socialInfo->relationship->id ?? ''"
                    :disabled="$readonly"
                />
            </div>
            <div class="col-12 col-md-6 col-lg-6">
                <x-form-select
                    name="civil_id"
                    label="7. Civil Status"
                    required="true"
                    :options="$civilStatus"
                    :selected="$socialInfo->civil->id ?? ''"
                    :disabled="$readonly"
                />
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-12 col-md-12 col-lg-4">
                <x-form-select 
                    name="religion_id"
                    label="8. Religion"
                    required="true"
                    :options="$religions"
                    :selected="$demographic->religion->id ?? ''"
                    :disabled="$readonly"
                />
            </div>
            <div class="col-12 col-md-12 col-lg-4">
                <x-form-select 
                    name="nationality_id"
                    label="9. Nationality"
                    required="true"
                    :options="$nationalities"
                    :selected="$demographic->nationality->id ?? ''"
                    :disabled="$readonly"
                />
            </div>
            <div class="col-12 col-md-12 col-lg-4">
                <x-form-select 
                    name="education_id"
                    label="10. Educational Attainment"
                    :options="$educations"
                    :selected="$socialInfo->education->id ?? ''"
                    :disabled="$readonly"
                />
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6 col-lg-6">
                <x-form-input 
                    name="skill"
                    label="11. Skills/Occupation"
                    type="text"
                    value="{{ $socialInfo->skill ?? null }}"
                    :readonly="$readonly"
                />
            </div>
            <div class="col-12 col-md-6 col-lg-6">
                <x-form-input 
                    name="income"
                    label="12. Estimated Monthly Income"
                    type="text"
                    value="{{ $socialInfo->income ?? null }}"
                    :readonly="$readonly"
                />
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-8 col-lg-6">
                <x-form-input
                    name="philhealth"
                    label="13. PhilHealth Number"
                    type="text"
                    value="{{ $socialInfo->philhealth ?? null }}"
                    :readonly="$readonly"
                />
            </div>
            <div class="col-12 col-md-8 col-lg-6">
                <x-form-input
                    name="contact_no"
                    label="14. Contact Number"
                    required="true"
                    type="text"
                    value="{{ $client->contact_no ?? null }}"
                    :readonly="$readonly"
                />
            </div>
        </div>
    </div>