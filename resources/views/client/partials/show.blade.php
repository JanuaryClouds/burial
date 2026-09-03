<div class="d-flex flex-column gap-4">
	<div class="row">
		<div class="col-12 col-md-8 col-xl-4">
			<x-display-field label="Full Name"
				contents="{{ $client->fullname() }}" />
		</div>
		<div class="col-12 col-md-4 col-xl-2">
			<x-display-field label="Date of Birth"
				contents="{{ Carbon\Carbon::parse($client->date_of_birth)->isoFormat('MMM DD, YYYY') }}" />
		</div>
		<div class="col-12 col-md-4 col-xl-1">
			<x-display-field label="Sex"
				contents="{{ $client->demographic->sex->name }}" />
		</div>
		<div class="col-12 col-md-4 col-xl-2">
			<x-display-field label="Civil Status"
				contents="{{ $client->socialInfo->civil->name ?? 'N/A' }}" />
		</div>
		<div class="col-12 col-md-4 col-xl-4">
			<x-display-field label="Address"
				contents="{{ $client->address() }}" />
		</div>
		<div class="col-12 col-md-4 col-xl-2">
			<x-display-field label="Contact Number"
				contents="{{ $client->contact_number }}" />
		</div>
		<div class="col-12 col-md-4 col-xl-3">
			<x-display-field label="PhilHealth ID"
				contents="{{ $client->socialInfo->philhealth ?? 'N/A' }}" />
		</div>
		<div class="col-12 col-md-4 col-xl-3">
			<x-display-field label="Educational Attainment"
				contents="{{ $client->socialInfo->education->name ?? 'N/A' }}" />
		</div>
		<div class="col-12 col-md-6 col-xl-2">
			<x-display-field label="Nationality"
				contents="{{ $client->demographic->nationality->name ?? 'N/A' }}" />
		</div>
		<div class="col-12 col-md-6 col-xl-2">
			<x-display-field label="Religion"
				contents="{{ $client->demographic->religion->name ?? 'N/A' }}" />
		</div>
		<div class="col-12 col-xl-6">
			<x-display-field label="Skills"
				contents="{{ $client->socialInfo->skill ?? 'N/A' }}" />
		</div>
		<div class="col-12 col-md-12 col-xl-2">
			<x-display-field label="Estimated Monthly Income"
				contents="{{ $client->socialInfo->income ?? 'N/A' }}" />
		</div>
	</div>
</div>
