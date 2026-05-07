@props([
    'readonly' => false,
    'client' => null,
])
@php
    $header = 'Client Information';
    if (isset($client)) {
        $first_name = $client->user?->first_name;
        $middle_name = $client->user?->middle_name;
        $last_name = $client->user?->last_name;
        $suffix = $client->user?->suffix;
        $contact_number = $client->user?->contact_number;

        $socialInfo = $client?->socialInfo;
        $demographic = $client?->demographic;
    } else {
        $first_name = session('citizen')['first_name'] ?? (auth()->user()?->first_name ?? null);
        $middle_name = session('citizen')['middle_name'] ?? (auth()->user()?->middle_name ?? null);
        $last_name = session('citizen')['last_name'] ?? (auth()->user()?->last_name ?? null);
        $suffix = session('citizen')['suffix'] ?? (auth()->user()?->suffix ?? null);
        $contact_number = session('citizen')['contact_number'] ?? (auth()->user()?->contact_number ?? null);
    }
@endphp
<h5 class="card-title">I. CLIENT'S IDENTIFYING INFORMATION</h5>
<div class="d-flex flex-column gap-4">
    <div class="row">
        <div class="col-6 col-md-6 col-lg-3">
            <x-form-input name="first_name" label="1.1 First Name" required="true" value="{{ $first_name ?? null }}"
                :readonly="$readonly" />
        </div>
        <div class="col-6 col-md-6 col-lg-2">
            <x-form-input name="middle_name" label="1.2 Middle Name" value="{{ $middle_name ?? null }}"
                :readonly="$readonly" />
        </div>
        <div class="col-8 col-md-8 col-lg-2">
            <x-form-input name="last_name" label="1.3 Last Name" required="true" value="{{ $last_name ?? null }}"
                :readonly="$readonly" />
        </div>
        <div class="col-4 col-md-4 col-lg-1">
            <x-form-input name="suffix" label="1.4 Suffix" value="{{ $suffix ?? null }}" :readonly="$readonly" />
        </div>
        <div class="col-4 col-md-4 col-lg-1">
            <x-form-input name="age" id="age" label="2. Age" required="true" type="number"
                value="{{ isset($client) ? $client->age() : session('citizen')['age'] ?? null }}" :readonly="$readonly" />
        </div>
        <div class="col-4 col-md-4 col-lg-1">
            <x-form-select name="sex_id" label="3. Sex" required="true" :selected="$demographic->sex_id ?? ($matched['sex_id'] ?? '')" :options="$genders ?? []"
                :disabled="$readonly" />
        </div>
        <div class="col-4 col-md-4 col-lg-2">
            <x-form-input name="date_of_birth" label="4. Date of Birth" required="true" type="date"
                value="{{ $client->date_of_birth ?? (session('citizen')['birthday'] ?? null) }}" :readonly="$readonly" />
        </div>
        <div class="col-4 col-md-4 col-lg-2">
            <x-form-input name="house_no" label="5.1. House Number" required="true" type="text"
                value="{{ $client->house_no ?? null }}" :readonly="$readonly" />
        </div>
        <div class="col-8 col-md-8 col-lg-4">
            <x-form-input name="street" label="5.2. Street" required="true" type="text"
                value="{{ $client->street ?? (session('citizen')['street'] ?? null) }}" :readonly="$readonly" />
        </div>
        <div class="col-6 col-md-6 col-lg-2">
            <x-form-select name="barangay_id" label="5.3 Barangay" required="true" :options="$barangays ?? []" :selected="$client->barangay_id ?? ($matched['barangay_id'] ?? '')"
                :disabled="$readonly" />
        </div>
        <div class="col-6 col-md-6 col-lg-2">
            <input type="hidden" name="district_id" id="district_id"
                value="{{ $client->district_id ?? ($matched['district_id'] ?? '') }}">
            <x-form-select name="district_id" label="5.4. District" required="true" :options="$districts ?? []"
                :selected="$client->district_id ?? ''" :disabled="true" id="district_id_display" />
        </div>
        <div class="col-12 col-md-12 col-lg-2">
            <x-form-input name="city" label="5.5. City" type="text" value="Taguig City" readonly="true" />
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-12 col-md-6 col-lg-6">
            <x-form-select name="relationship_id" label="6. Relationship to Beneficiary" required="true"
                :options="$relationships ?? []" :selected="$socialInfo->relationship_id ?? ''" :disabled="$readonly" />
        </div>
        <div class="col-12 col-md-6 col-lg-6">
            <x-form-select name="civil_id" label="7. Civil Status" required="true" :options="$civilStatus ?? []" :selected="$socialInfo->civil_id ?? ($matched['civil_id'] ?? 1)"
                :disabled="$readonly" />
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-12 col-md-12 col-lg-4">
            <x-form-select name="religion_id" label="8. Religion" required="true" :options="$religions ?? []" :selected="$demographic->religion_id ?? ''"
                :disabled="$readonly" />
        </div>
        <div class="col-12 col-md-12 col-lg-4">
            <x-form-select name="nationality_id" label="9. Nationality" required="true" :options="$nationalities ?? []"
                :selected="$demographic->nationality_id ?? ''" :disabled="$readonly" />
        </div>
        <div class="col-12 col-md-12 col-lg-4">
            <x-form-select name="education_id" label="10. Educational Attainment" required="true" :options="$educations ?? []"
                :selected="$socialInfo->education_id ?? ''" :disabled="$readonly" />
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-6 col-lg-3">
            <x-form-input name="skill" label="11. Skills/Occupation" type="text"
                value="{{ $socialInfo->skill ?? null }}" :readonly="$readonly" />
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <x-form-input name="income" label="12. Estimated Monthly Income" type="text"
                value="{{ $socialInfo->income ?? null }}" :readonly="$readonly" />
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <x-form-input name="philhealth" label="13. PhilHealth Number" type="text"
                value="{{ $socialInfo->philhealth ?? null }}" :readonly="$readonly" />
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <x-form-input name="contact_number" label="14. Contact Number" required="true" type="text"
                value="{{ $contact_number ?? null }}" :readonly="$readonly" />
        </div>
    </div>
</div>
<script {{ $nonce ? 'nonce="' . $nonce . '"' : '' }}>
    $(document).ready(function() {
        $('#date_of_birth').on('input', function() {
            let birthdate = new Date($('#date_of_birth').val());
            let now = new Date();
            let age = now.getFullYear() - birthdate.getFullYear();
            let monthDiff = now.getMonth() - birthdate.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && now.getDate() < birthdate.getDate())) {
                age--;
            }
            $('#age').val(age);
        })

        function updateDistrict(barangay) {
            if ([
                    'Pateros',
                    'Bagumbayan',
                    'Bambang',
                    'Calzada',
                    'Comembo',
                    'Hagonoy',
                    'Ibayo-tipas',
                    'Ligid-tipas',
                    'Lower bicutan',
                    'New lower bicutan',
                    'Napindan',
                    'Palingon',
                    'Pembo',
                    'Rizal',
                    'San miguel',
                    'Sta Ana',
                    'Tuktukan',
                    'Ususan',
                    'Wawa',
                ].includes(barangay)) {
                $('#district_id').val(1);
                $('#district_id_display').val(1);
            } else {
                $('#district_id').val(2);
                $('#district_id_display').val(2);
            }
        }

        $('#barangay_id').on('change', function() {
            updateDistrict($(this).find('option:selected').text());
        })

        updateDistrict($('#barangay_id option:selected').text());
    })
</script>
