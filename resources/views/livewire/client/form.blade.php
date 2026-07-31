<x-row>
	<div class="col-12 col-md-6 col-lg-4">
		<x-form.input name="first_name"
			label="First Name"
			:required="true"
			value="{{ $firstName ?? null }}"
			:readonly="true" />
	</div>
	<div class="col-12 col-md-6 col-lg-3">
		<x-form.input name="middle_name"
			label="Middle Name"
			:required="false"
			value="{{ $middleName ?? null }}"
			:readonly="true" />
	</div>
	<div class="col-8 col-md-6 col-lg-3">
		<x-form.input name="last_name"
			label="Last Name"
			:required="true"
			value="{{ $lastName ?? null }}"
			:readonly="true" />
	</div>
	<div class="col-4 col-md-2 col-lg-2">
		<x-form.input name="suffix"
			label="Suffix"
			value="{{ $suffix ?? null }}"
			:readonly="true" />
	</div>
	<div class="col-6 col-md-4 col-lg-3 col-xl-2">
		<x-form.input name="date_of_birth"
			label="Date of Birth"
			:required="true"
			value="{{ $dateOfBirth ?? null }}"
			type="date" />
	</div>
	<div class="col-6 col-md-4 col-lg-2 col-xl-2">
		<x-form.select name="sex_id"
			label="Sex"
			:options="$genders ?? []"
			:selected="$demographic->sex_id ?? ($matched['sex_id'] ?? '')"
			:required="true" />
	</div>
	<div class="col-6 col-md-4 col-lg-3 col-xl-2">
		<x-form.select name="civil_id"
			label="Civil Status"
			:options="$civilStatus ?? []"
			:selected="$socialInfo->civil_id ?? ($matched['civil_id'] ?? 1)"
			:required="true" />
	</div>
	<div class="col-6 col-md-4 col-lg-4 col-xl-2">
		<x-form.select name="nationality_id"
			label="Nationality"
			:options="$nationalities ?? []"
			:selected="$demographic->nationality_id ?? ''"
			:required="true" />
	</div>
	<div class="col-12 col-lg-5 col-xl-4">
		<x-form.select name="religion_id"
			label="Religion"
			:options="$religions ?? []"
			:selected="$demographic->religion_id ?? ''"
			:required="true" />
	</div>
	<div class="separator my-4"></div>
	<div class="col-5 col-md-3 col-lg-3 col-xl-2">
		<x-form-input name="house_no"
			label="House Number"
			value="{{ $client->house_no ?? null }}"
			:required="true" />
	</div>
	<div class="col-7 col-md-4 col-lg-9 col-xl-3">
		<x-form-input name="street"
			label="Street"
			value="{{ $client->street ?? (session('citizen')['street'] ?? null) }}"
			:required="true" />
	</div>
	<div class="col-8 col-md-3 col-lg-4 col-xl-2">
		<x-form.select id="barangay_id"
			name="barangay_id"
			label="Barangay"
			:options="$barangays ?? ($matched['barangay_id'] ?? '')"
			:selected="$client->barangay_id ?? ($matched['barangay_id'] ?? '')"
			:required="true" />
	</div>
	<div class="col-4 col-md-2 col-lg-2 col-xl-1">
		<input type="hidden"
			name="district_id"
			id="district_id"
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
			value="{{ $contactNumber ?? null }}"
			:required="true" />
	</div>
	<div class="separator my-4"></div>
	<div class="col-6 col-lg-6 col-xl-3">
		<x-form.select name="education_id"
			label="Educational Attainment"
			:options="$educations ?? []"
			:selected="$socialInfo->education_id ?? ''" />
	</div>
	<div class="col-6 col-lg-6 col-xl-3">
		<x-form.input name="philhealth"
			label="PhilHealth ID"
			value="{{ $socialInfo->philhealth ?? null }}" />
	</div>
	<div class="col-12 col-md-6 col-lg-6 col-xl-3">
		<x-form.input name="skill"
			label="Skills/Occupation"
			value="{{ $socialInfo->skill ?? null }}" />
	</div>
	<div class="col-12 col-md-6 col-lg-6 col-xl-3">
		<x-form.input name="income"
			label="Estimated Monthly Income"
			value="{{ $socialInfo->income ?? null }}" />
	</div>
</x-row>
