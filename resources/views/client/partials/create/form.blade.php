@props([
    'client' => null,
    'readonly' => false,
])

@php
	if (isset($client)) {
	    $firstName = $client->user->first_name;
	    $middleName = $client->user->middle_name ?? null;
	    $lastName = $client->user->last_name;
	    $suffix = $client->user->suffix ?? null;
	    $contactNumber = $client->contact_number;
	    $socialInfo = $client->socialInfo;
	    $demographic = $client->demographic;
	    $dateOfBirth = $client->date_of_birth;
	} else {
	    $firstName = session('citizen')['first_name'] ?? (auth()->user()?->first_name ?? null);
	    $middleName = session('citizen')['middle_name'] ?? (auth()->user()?->middle_name ?? null);
	    $lastName = session('citizen')['last_name'] ?? (auth()->user()?->last_name ?? null);
	    $suffix = session('citizen')['suffix'] ?? (auth()->user()?->suffix ?? null);
	    $contactNumber = session('citizen')['contact_number'] ?? (auth()->user()?->contact_number ?? null);
	    $dateOfBirth = session('citizen')['birthday'] ?? null;
	}
@endphp
<x-row>
	<div class="col-12 col-md-6 col-lg-4">
		<x-form.input name="first_name"
			label="First Name"
			:required="true"
			value="{{ $firstName ?? null }}"
			:readonly="$readonly" />
	</div>
	<div class="col-12 col-md-6 col-lg-3">
		<x-form.input name="middle_name"
			label="Middle Name"
			:required="false"
			value="{{ $middleName ?? null }}"
			:readonly="$readonly" />
	</div>
	<div class="col-8 col-md-6 col-lg-3">
		<x-form.input name="last_name"
			label="Last Name"
			:required="true"
			value="{{ $lastName ?? null }}"
			:readonly="$readonly" />
	</div>
	<div class="col-4 col-md-2 col-lg-2">
		<x-form.input name="suffix"
			label="Suffix"
			value="{{ $suffix ?? null }}"
			:readonly="$readonly" />
	</div>
	<div class="col-6 col-md-4 col-lg-3 col-xl-2">
		<x-form.input name="date_of_birth"
			label="Date of Birth"
			max="{{ now() }}"
			:required="true"
			:readonly="$readonly"
			value="{{ $dateOfBirth ?? null }}"
			type="date" />
	</div>
	<div class="col-6 col-md-4 col-lg-2 col-xl-2">
		<x-form.select name="sex_id"
			label="Sex"
			:options="$genders ?? []"
			:readonly="$readonly"
			:selected="$demographic->sex_id ?? ($matched['sex_id'] ?? '')"
			:required="true" />
	</div>
	<div class="col-6 col-md-4 col-lg-3 col-xl-2">
		<x-form.select name="civil_id"
			label="Civil Status"
			:options="$civilStatus ?? []"
			:readonly="$readonly"
			:selected="$socialInfo->civil_id ?? ($matched['civil_id'] ?? 1)"
			:required="true" />
	</div>
	<div class="col-6 col-md-4 col-lg-4 col-xl-2">
		<x-form.select name="nationality_id"
			label="Nationality"
			:options="$nationalities ?? []"
			:readonly="$readonly"
			:selected="$demographic->nationality_id ?? ''"
			:required="true" />
	</div>
	<div class="col-12 col-lg-5 col-xl-4">
		<x-form.select name="religion_id"
			label="Religion"
			:options="$religions ?? []"
			:readonly="$readonly"
			:selected="$demographic->religion_id ?? ''"
			:required="true" />
	</div>
	<div class="separator my-4"></div>
	<div class="col-5 col-md-3 col-lg-3 col-xl-2">
		<x-form-input name="house_no"
			label="House Number"
			:readonly="$readonly"
			value="{{ $client->house_no ?? null }}"
			:required="true" />
	</div>
	<div class="col-7 col-md-4 col-lg-9 col-xl-3">
		<x-form-input name="street"
			label="Street"
			:readonly="$readonly"
			value="{{ $client->street ?? (session('citizen')['street'] ?? null) }}"
			:required="true" />
	</div>
	<div class="col-8 col-md-3 col-lg-4 col-xl-2">
		<x-form.select id="barangay_id"
			name="barangay_id"
			label="Barangay"
			:options="$barangays ?? ($matched['barangay_id'] ?? '')"
			:readonly="$readonly"
			:selected="$client->barangay_id ?? ($matched['barangay_id'] ?? '')"
			:required="true" />
	</div>
	<div class="col-4 col-md-2 col-lg-2 col-xl-1">
		<input type="hidden"
			name="district_id"
			id="district_id"
			:readonly="$readonly"
			value="{{ $client->district_id ?? '' }}" />
		<x-form.input id="district_id_display"
			name="district_id_display"
			label="District"
			value="{{ $client->district_id ?? '' }}"
			:readonly="true"
			:required="true" />
	</div>
	<div class="col-6 col-lg-3 col-xl-2">
		<x-form.input name="city"
			label="City"
			type="text"
			value="Taguig City"
			:readonly="true"
			:required="true" />
	</div>
	<div class="col-6 col-lg-3 col-xl-2">
		<x-form.input name="contact_number"
			label="Contact Number"
			type="text"
			:readonly="$readonly"
			value="{{ $contactNumber ?? null }}"
			:required="true" />
	</div>
	<div class="separator my-4"></div>
	<div class="col-6 col-lg-6 col-xl-3">
		<x-form.select name="education_id"
			label="Educational Attainment"
			:readonly="$readonly"
			:options="$educations ?? []"
			:selected="$socialInfo->education_id ?? ''" />
	</div>
	<div class="col-6 col-lg-6 col-xl-3">
		<x-form.input name="philhealth"
			label="PhilHealth ID"
			:readonly="$readonly"
			value="{{ $socialInfo->philhealth ?? null }}" />
	</div>
	<div class="col-12 col-md-6 col-lg-6 col-xl-3">
		<x-form.input name="skill"
			label="Skills/Occupation"
			:readonly="$readonly"
			value="{{ $socialInfo->skill ?? null }}" />
	</div>
	<div class="col-12 col-md-6 col-lg-6 col-xl-3">
		<x-form.input name="income"
			label="Estimated Monthly Income"
			:readonly="$readonly"
			value="{{ $socialInfo->income ?? null }}" />
	</div>
</x-row>
