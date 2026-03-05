@php
    use App\Models\Sex;
    use App\Models\Religion;
    $genders = Sex::getAllSexes();
    $religions = Religion::getAllReligions();
@endphp
@props(['beneficiary', 'readonly' => false, 'disabled' => false])
<h2>Deceased Information</h2>
<p>Please fill out the following information. Fields marked with an asterisk are required. Leave blank if inapplicable.
</p>

<div class="row justify-content-start align-items-center g-2">
    <div class="col-12 col-md-6 col-lg-6">
        <x-form-input name="beneficiary[first_name]" label="Given Name" required="true"
            value="{{ $beneficiary->first_name ?? '' }}" disabled="{{ $disabled }}" readonly="{{ $readonly }}" />
    </div>
    <div class="col-12 col-md-4 col-lg-2">
        <x-form-input name="beneficiary[middle_name]" label="Middle Name" value="{{ $beneficiary->middle_name ?? '' }}"
            disabled="{{ $disabled }}" readonly="{{ $readonly }}" />
    </div>
    <div class="col-6 col-lg-2">
        <x-form-input name="beneficiary[last_name]" label="Last Name" required="true"
            value="{{ $beneficiary->last_name ?? '' }}" disabled="{{ $disabled }}" readonly="{{ $readonly }}" />
    </div>
    <div class="col-6 col-lg-2">
        <x-form-input name="beneficiary[suffix]" label="Suffix" value="{{ $beneficiary->suffix ?? '' }}"
            disabled="{{ $disabled }}" readonly="{{ $readonly }}" />
    </div>
</div>
<div class="row justify-content-start align-items-center g-2">
    <div class="col-12 col-lg-8">
        <x-form-input name="beneficiary[place_of_birth]" label="Address" value="{{ $beneficiary->address() ?? '' }}"
            disabled="{{ $disabled }}" readonly="{{ $readonly }}" />
    </div>
    <div class="col-12 col-lg-4">
        <x-form-select name="beneficiary[barangay_id]" label="Barangay" required="true" :options="$barangays"
            :selected="$beneficiary->barangay_id ?? ''" disabled="{{ $disabled }}" />
    </div>
</div>
<div class="row justify-content-start align-items-center g-2">
    <div class="col-12 col-lg-3">
        <x-form-select name="beneficiary[sex_id]" label="Gender" required="true" :options="$genders->pluck('name', 'id')" :selected="$beneficiary->sex->id ?? ''"
            disabled="{{ $disabled }}" />
    </div>
    <div class="col-12 col-lg-3">
        <x-form-select name="beneficiary[religion_id]" label="Religion" required="true" :options="$religions->pluck('name', 'id')"
            :selected="$beneficiary->religion_id ?? ''" disabled="{{ $disabled }}" id="religion" />
    </div>
    <div class="col-12 col-lg-3">
        <x-form-input name="beneficiary[date_of_birth]" label="Date of Birth" required="true" type="date"
            value="{{ $beneficiary->date_of_birth ?? '' }}" disabled="{{ $disabled }}"
            readonly="{{ $readonly }}" />
    </div>
    <div class="col-12 col-lg-3">
        <x-form-input name="beneficiary[date_of_death]" label="Date of Death" required="true" type="date"
            value="{{ $beneficiary->date_of_death ?? '' }}" disabled="{{ $disabled }}"
            readonly="{{ $readonly }}" />
    </div>
</div>
