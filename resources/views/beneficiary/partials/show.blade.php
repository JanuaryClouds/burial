<div class="d-flex flex-column gap-4">
	<div class="row">
		<div class="col-12 col-md-8 col-xl-4">
			<x-display-field label="Full Name"
				contents="{{ $beneficiary->fullname() }}" />
		</div>
		<div class="col-12 col-md-4 col-xl-2">
			<x-display-field label="Date of Birth"
				contents="{{ Carbon\Carbon::parse($beneficiary->date_of_birth)->isoFormat('MMM DD, YYYY') }}" />
		</div>
		<div class="col-12 col-md-4 col-xl-2">
			<x-display-field label="Date of Death"
				contents="{{ Carbon\Carbon::parse($beneficiary->date_of_death)->isoFormat('MMM DD, YYYY') }}" />
		</div>
		<div class="col-12 col-md-4 col-xl-1">
			<x-display-field label="Sex"
				contents="{{ $beneficiary->sex->name }}" />
		</div>
		<div class="col-12 col-md-4 col-xl-3">
			<x-display-field label="Religion"
				contents="{{ $beneficiary->religion->name ?? 'N/A' }}" />
		</div>
		<div class="col-12 col-md-12 col-xl-6">
			<x-display-field label="Address"
				contents="{{ $beneficiary->address() }}" />
		</div>
	</div>
</div>
