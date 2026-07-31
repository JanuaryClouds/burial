@props([
    'beneficiary' => null,
    'readonly' => false,
])

@php
	if (isset($beneficiary)) {
	    $firstName = $beneficiary->first_name ?? null;
	    $middleName = $beneficiary->middle_name ?? null;
	    $lastName = $beneficiary->last_name ?? null;
	    $suffix = $beneficiary->suffix ?? null;
	    $sexId = $beneficiary->sex_id ?? null;
	    $religionId = $beneficiary->religion_id ?? null;
	    $dateOfBirth = $beneficiary->date_of_birth ?? null;
	    $dateOfDeath = $beneficiary->date_of_death ?? null;
	    $houseNo = $beneficiary->house_no ?? null;
	    $street = $beneficiary->street ?? null;
	    $barangayId = $beneficiary->barangay_id ?? null;
	    $districtId = $beneficiary->district_id ?? null;
	    $city = $beneficiary->city ?? null;
	}
@endphp

<div class="row">
	<div class="col-12 col-md-6 col-lg-4">
		<x-form.input name="first_name"
			label="First Name"
			:readonly="$readonly"
			value="{{ $firstName ?? null }}"
			:required="true" />
	</div>
	<div class="col-12 col-md-6 col-lg-3">
		<x-form.input name="middle_name"
			label="Middle Name"
			value="{{ $middleName ?? null }}"
			:readonly="$readonly"
			:required="false" />
	</div>
	<div class="col-9 col-md-9 col-lg-3">
		<x-form.input name="last_name"
			label="Last Name"
			value="{{ $lastName ?? null }}"
			:readonly="$readonly"
			:required="true" />
	</div>
	<div class="col-3 col-md-3 col-lg-2">
		<x-form.input name="suffix"
			label="Suffix"
			value="{{ $suffix ?? null }}"
			:readonly="$readonly"
			:required="false" />
	</div>
	<div class="col-12 col-md-4 col-lg-2">
		<x-form.select name="sex_id"
			label="Sex"
			:required="true"
			:selected="$sexId ?? null"
			:readonly="$readonly"
			:options="$genders ?? []" />
	</div>
	<div class="col-6 col-md-6 col-lg-2">
		<x-form.input name="date_of_birth"
			label="Date of Birth"
			max="{{ now() }}"
			value="{{ $dateOfBirth ?? null }}"
			:required="true"
			:readonly="$readonly"
			type="date" />
	</div>
	<div class="col-6 col-md-6 col-lg-2">
		<x-form.input name="date_of_death"
			label="Date of Death"
			value="{{ $dateOfDeath ?? null }}"
			max="{{ now() }}"
			:required="true"
			:readonly="$readonly"
			type="date" />
	</div>
	<div class="col-12 col-md-4 col-lg-4">
		<x-form.select name="religion_id"
			label="Religion"
			:selected="$religionId ?? null"
			:required="true"
			:readonly="$readonly"
			:options="$religions ?? []" />
	</div>
	<div class="separator my-4"></div>
	<div class="col-12 col-md-3 col-lg-3 col-xl-2">
		<x-form.input name="house_no"
			label="House No."
			value="{{ $houseNo ?? null }}"
			:readonly="$readonly"
			:required="true" />
	</div>
	<div class="col-12 col-md-4 col-lg-9 col-xl-3">
		<x-form.input name="street"
			value="{{ $street ?? null }}"
			label="Street"
			:readonly="$readonly"
			:required="true" />
	</div>
	<div class="col-8 col-md-4 col-lg-4 col-xl-4">
		<x-form.select name="barangay_id"
			label="Barangay"
			:options="$barangays ?? []"
			:selected="$barangayId ?? null"
			:readonly="$readonly"
			:required="true" />
	</div>
	<div class="col-4 col-md-4 col-lg-3 col-xl-4">
		<input type="hidden"
			value="{{ $districtId ?? null }}"
			name="district_id"
			id="district_id" />
		<x-form.input name="district_id_display"
			label="District"
			value="{{ $districtId ?? null }}"
			:readonly="true"
			:required="true" />
	</div>
	<div class="col-12 col-md-6 col-lg-3">
		<x-form.input name="city"
			label="City"
			type="text"
			value="Taguig City"
			:readonly="true"
			:required="true" />
	</div>
</div>
<script nonce={{ $nonce ?? '' }}>
	$('#date_of_birth').on('change', function() {
		let value = $(this).val();
		if (value) {
			$('#date_of_death').attr('min', value);
		} else {
			$('#date_of_death').attr('min', '');
		}
	});

	$('#date_of_death').on('change', function() {
		let value = $(this).val();
		if (value) {
			$('#date_of_birth').attr('max', value);
		} else {
			$('#date_of_birth').attr('max', '');
		}
	});
</script>
