<x-slot:pageTitle>{{ $beneficiary->fullname() }} | Beneficiary</x-slot:pageTitle>
<x-slot:pageSubTitle>Funeral Assistance System | CSWDO Taguig</x-slot:pageSubTitle>
<div class="d-flex flex-column gap-4">
	{{-- start::Basic Information --}}
	<h4>Basic Information</h4>
	<div class="row">
		<div class="col-12 col-md-8 col-lg-6">
			<x-form.display label="Name"
				:contents="$beneficiary->fullname()" />
		</div>
		<div class="col-4 col-md-4 col-lg-2">
			<x-form.display label="Sex"
				:contents="$beneficiary->sex->name" />
		</div>
		<div class="col-6 col-md-4 col-lg-4 col-xl-3">
			<x-form.display label="Person with Disability"
				:contents="$beneficiary->pwd ? 'Yes' : 'No'" />
		</div>
		<div class="col-6 col-md-4 col-lg-3 col-xl-2">
			<x-form.display label="Date of Birth"
				:contents="\Carbon\Carbon::parse($beneficiary->date_of_birth)->format('F d, Y')" />
		</div>
		<div class="col-6 col-md-4 col-lg-3 col-xl-2">
			<x-form.display label="Date of Death"
				:contents="\Carbon\Carbon::parse($beneficiary->date_of_death)->format('F d, Y')" />
		</div>
		<div class="col-6 col-md-4 col-lg-3 col-xl-2">
			<x-form.display label="Age"
				:contents="$beneficiary->age() . ' years old'" />
		</div>
	</div>

	<div class="separator separator-dashed my-4"></div>

	{{-- start::Social Information --}}
	<h4>Social Information</h4>
	<div class="row">
		<div class="col-12 col-md-6 col-lg-4 col-xl-3">
			<x-form.display label="Religion"
				:contents="$beneficiary->religion->name" />
		</div>
	</div>
	{{-- end::Social Information --}}

	<div class="separator separator-dashed my-4"></div>

	{{-- start::Address --}}
	<h4>Place of Birth</h4>
	<div class="row">
		<div class="col-12 col-md-5 col-xl-3">
			<x-form.display label="House Number"
				:contents="$beneficiary->house_no" />
		</div>
		<div class="col-12 col-md-7 col-xl-5">
			<x-form.display label="Street"
				:contents="$beneficiary->street" />
		</div>
		<div class="col-6 col-md-6 col-xl-2">
			<x-form.display label="Barangay"
				:contents="$beneficiary->barangay->name" />
		</div>
		<div class="col-6 col-xl-2">
			<x-form.display label="City"
				:contents="$beneficiary->city" />
		</div>
	</div>
	{{-- end::Address --}}
</div>
