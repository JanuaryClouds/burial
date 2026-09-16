<x-slot:pageTitle>{{ $client->fullname() }} | Client</x-slot:pageTitle>
<x-slot:pageSubTitle>Funeral Assistance System | CSWDO Taguig</x-slot:pageSubTitle>
<div class="d-flex flex-column gap-4">
	{{-- start::Basic Information --}}
	<h4>Basic Information</h4>
	<div class="row">
		<div class="col-12 col-md-6">
			<x-form.display label="Name"
				:contents="$client->fullname()" />
		</div>
		<div class="col-6 col-md-3 col-xl-2">
			<x-form.display label="Date of Birth"
				:contents="\Carbon\Carbon::parse($client->date_of_birth)->format('F d, Y')" />
		</div>
		<div class="col-6 col-md-3 col-xl-2">
			<x-form.display label="Sex"
				:contents="$client->demographic->sex->name" />
		</div>
	</div>
	{{-- end::Basic Information --}}

	<div class="separator separator-dashed my-4"></div>

	{{-- start::Address --}}
	<h4>Address</h4>
	<div class="row">
		<div class="col-12 col-md-4 col-lg-4 col-xl-2">
			<x-form.display label="House Number"
				:contents="$client->house_no" />
		</div>
		<div class="col-12 col-md-8 col-lg-8 col-xl-5">
			<x-form.display label="Street"
				:contents="$client->street" />
		</div>
		<div class="col-8 col-lg-6 col-xl-3">
			<x-form.display label="Barangay"
				:contents="$client->barangay->name" />
		</div>
		<div class="col-4 col-lg-6 col-xl-2">
			<x-form.display label="City"
				:contents="$client->city" />
		</div>
	</div>
	{{-- end::Address --}}

	<div class="separator separator-dashed my-4"></div>

	{{-- start::Social Information --}}
	<h4>Social Information</h4>
	<div class="row">
		<div class="col-6 col-md-4 col-lg-4 col-xl-2">
			<x-form.display label="Contact Number"
				:contents="$client->contact_number" />
		</div>
		<div class="col-6 col-md-3 col-xl-2">
			<x-form.display label="Civil Status"
				:contents="$client->socialInfo->civil->name" />
		</div>
		<div class="col-12 col-md-5 col-xl-3">
			<x-form.display label="Nationality"
				:contents="$client->demographic->nationality->name" />
		</div>
		<div class="col-12 col-md-6 col-xl-4">
			<x-form.display label="Religion"
				:contents="$client->demographic->religion->name" />
		</div>
		<div class="col-12 col-md-6 col-xl-4">
			<x-form.display label="Educational Attainment"
				:contents="$client->socialInfo->education?->name ?? 'N/A'" />
		</div>
		<div class="col-12 col-md-6 col-xl-3">
			<x-form.display label="PhilHealth ID"
				:contents="$client->socialInfo->philHealth ?? 'N/A'" />
		</div>
		<div class="col-12 col-md-6 col-xl-3">
			<x-form.display label="Skills/Occupation"
				:contents="$client->socialInfo->skill ?? 'N/A'" />
		</div>
		<div class="col-6 col-md-4 col-xl-3">
			<x-form.display label="Estimated Monthly Income"
				:contents="$client->socialInfo->income ?? 'N/A'" />
		</div>
	</div>
	{{-- end::Social Information --}}
</div>
