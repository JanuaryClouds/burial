@php
    use App\Models\Sex;
    use App\Models\Religion;
    $genders = Sex::getAllSexes();
    $religions = Religion::getAllReligions();
@endphp
@props([
    'deceased',
    'readonly' => false,
    'disabled' => false,
])

<div class="bg-white shadow-sm rounded p-4">
    <h2>Deceased Information</h2>
    <p>Please fill out the following information. Fields marked with an asterisk are required. Leave blank if inapplicable.</p>
    
    <div
        class="row justify-content-start align-items-center g-2"
    >
        <div
            class="col-12 col-md-6 col-lg-6"
        >
            <x-form-input 
                name="deceased[first_name]"
                label="Given Name"
                required="true"
                value="{{ $deceased->first_name ?? '' }}"
                disabled="{{ $disabled }}"
                readonly="{{ $readonly }}"
            />
        </div>
        <div
            class="col-12 col-md-4 col-lg-2"
        >
            <x-form-input 
                name="deceased[middle_name]"
                label="Middle Name"
                value="{{ $deceased->middle_name ?? '' }}"
                disabled="{{ $disabled }}"
                readonly="{{ $readonly }}"
                />
            </div>
        <div
            class="col-6 col-lg-2"
        >
            <x-form-input 
                name="deceased[last_name]"
                label="Last Name"
                required="true"
                value="{{ $deceased->last_name ?? '' }}"
                disabled="{{ $disabled }}"
                readonly="{{ $readonly }}"
            />
        </div>
        <div
            class="col-6 col-lg-2"
        >
            <x-form-input
                name="deceased[suffix]"
                label="Suffix"
                value="{{ $deceased->suffix ?? '' }}"
                disabled="{{ $disabled }}"
                readonly="{{ $readonly }}"
            />
        </div>            
    </div>
    <div class="row justify-content-start align-items-center g-2">
        <div class="col-12 col-lg-8">
            <x-form-input
                name="deceased[address]"
                label="Address"
                value="{{ $deceased->address ?? '' }}"
                disabled="{{ $disabled }}"
                readonly="{{ $readonly }}"
            />
        </div>
        <div class="col-12 col-lg-4">
            <x-form-select
                name="deceased[barangay_id]"
                label="Barangay"
                required="true"
                :options="$barangays->pluck('name', 'id')"
                :selected="$deceased->barangay_id ?? ''"
                disabled="{{ $disabled }}"
            />
        </div>
    </div>
    <div
        class="row justify-content-start align-items-center g-2"
    >
        <div class="col-12 col-lg-3">
            <x-form-select
                name="deceased[gender]"
                label="Gender"
                required="true"
                :options="$genders->pluck('name', 'id')"
                :selected="$deceased->gender ?? ''"
                disabled="{{ $disabled }}"
            />
        </div>
        <div class="col-12 col-lg-3">
            <x-form-select
                name="deceased[religion_id]"
                label="Religion"
                required="true"
                :options="$religions->pluck('name', 'id')"
                :selected="$deceased->religion_id ?? ''"
                disabled="{{ $disabled }}"
                id="religion"  
            />
        </div>
        <div class="col-12 col-lg-3">
            <x-form-input
                name="deceased[date_of_birth]"
                label="Date of Birth"
                required="true"
                type="date"
                value="{{ $deceased->date_of_birth ?? '' }}"
                disabled="{{ $disabled }}"
                readonly="{{ $readonly }}"
            />
        </div>
        <div class="col-12 col-lg-3">
            <x-form-input
                name="deceased[date_of_death]"
                label="Date of Death"
                required="true"
                type="date"
                value="{{ $deceased->date_of_death ?? '' }}"
                disabled="{{ $disabled }}"
                readonly="{{ $readonly }}"
            />
        </div>
    </div>
</div>