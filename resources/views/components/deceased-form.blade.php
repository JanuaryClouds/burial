@php
    use App\Models\Sex;
    $genders = Sex::getAllSexes();
@endphp
@props([
    'deceased',
    'readonly' => false,
    'disabled' => false,
])

<div class="container bg-white shadow rounded p-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2>Deceased Information</h2>
    </div>
    <p>Please fill out the following information. Fields marked with an asterisk are required. Leave blank if inapplicable.</p>
    <div
        class="row justify-content-center align-items-center g-2"
    >
        <div
            class="col-6"
        >
            <x-form-input 
                name="deceased[first_name]"
                placeholder="Juan"
                label="Given Name"
                required="true"
                value="{{ $deceased->first_name ?? '' }}"
                disabled="{{ $disabled }}"
                readonly="{{ $readonly }}"
            />
            </div>
            <div
            class="col-2"
            >
            <x-form-input 
                name="deceased[middle_name]"
                placeholder="Santos"
                label="Middle Name"
                value="{{ $deceased->middle_name ?? '' }}"
                disabled="{{ $disabled }}"
                readonly="{{ $readonly }}"
                />
            </div>
        <div
            class="col-2"
        >
            <x-form-input 
                name="deceased[last_name]"
                placeholder="Dela Cruz"
                label="Last Name"
                required="true"
                value="{{ $deceased->last_name ?? '' }}"
                disabled="{{ $disabled }}"
                readonly="{{ $readonly }}"
            />
        </div>
        <div
            class="col-2"
        >
            <x-form-input
                name="deceased[suffix]"
                placeholder="Jr."
                label="Suffix"
                value="{{ $deceased->suffix ?? '' }}"
                disabled="{{ $disabled }}"
                readonly="{{ $readonly }}"
            />
        </div>            
    </div>
    <div
        class="row justify-content-start align-items-center g-2"
    >
        <div class="col-3">
            <x-form-select
                name="deceased[gender]"
                label="Gender"
                required="true"
                :options="$genders->pluck('name', 'id')"
                :selected="$deceased->gender ?? ''"
                disabled="{{ $disabled }}"
            />
        </div>
        <div class="col-3">
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
        <div class="col-3">
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