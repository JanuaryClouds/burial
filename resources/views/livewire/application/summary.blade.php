<div>
	<div class="row mb-4">
		<div class="col-12 col-lg-6">
			<div class="d-flex flex-column gap-2">
				<span>
					<strong>Tracking Number:</strong> {{ $application->tracking_no }}
				</span>
				<span>
					<strong>Submitted on:</strong> {{ \Carbon\Carbon::parse($application->created_at)->format('F d, Y') }}
				</span>
				<span>
					<strong>Relationship of Client to Beneficiary:</strong>
					{{ $application->relationship->name }}
				</span>
			</div>
		</div>
	</div>
	<div class="row"
		wire:poll='30s'>
		<div class="col-12 col-lg-6">
			<div class="border border-2 border-dashed border-gray-300 rounded px-4 py-3 mb-4">
				<div class="d-flex flex-column gap-2">
					<h4>Client</h4>
					<span>
						<strong>Name:</strong> {{ $client->fullname() }}
					</span>
					<span>
						<strong>Address:</strong> {{ $client->address() }}
					</span>
					<span>
						<strong>Contact Number:</strong> {{ $client->contact_number }}
					</span>
				</div>
			</div>
		</div>
		<div class="col-12 col-lg-6">
			<div class="border border-2 border-dashed border-gray-300 rounded px-4 py-3 mb-4">
				<div class="d-flex flex-column gap-2">
					<h4>Beneficiary</h4>
					<span>
						<strong>Name:</strong> {{ $beneficiary->fullname() }}
					</span>
					<span>
						<strong>Date of Birth:</strong> {{ \Carbon\Carbon::parse($beneficiary->date_of_birth)->format('F d, Y') }}
					</span>
					<span>
						<strong>Date of Death:</strong> {{ \Carbon\Carbon::parse($beneficiary->date_of_death)->format('F d, Y') }}
						({{ $beneficiary->age() }} years old)
					</span>
				</div>
			</div>
		</div>
	</div>
</div>
