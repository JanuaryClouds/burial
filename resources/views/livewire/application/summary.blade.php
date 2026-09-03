<x-card id="application-header">
	<x-slot:header>
		Application Summary
	</x-slot:header>
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
	<div class="row">
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
		@if ($application->assessment)
			<div class="col-12 col-lg-6 mb-4">
				<livewire:assessment.show :assessment="$application->assessment" />
			</div>
		@endif
		@if ($application->recommendations->count() > 0)
			@php
				$latestRecommendation = $application->recommendations->sortByDesc('created_at')->first();
			@endphp
			<div class="col-12 col-lg-6 mb-4">
				<livewire:recommendation.show :recommendation="$latestRecommendation" />
			</div>
		@endif
	</div>
	<x-slot:footer>
		<a class="btn btn-sm btn-light"
			href="{{ route('client.show', $client) }}"
			role="button">
			<x-icon.font-awesome :icon="'arrow-up-right-from-square'" />
			View Client
		</a>
		<a class="btn btn-sm btn-light"
			href="{{ route('beneficiary.show', $beneficiary) }}"
			role="button">
			<x-icon.font-awesome :icon="'arrow-up-right-from-square'" />
			View Beneficiary
		</a>
		<x-modal :modalId="'tracker-slip-modal'"
			:modalSize="'md'"
			:modalTitle="'Application Tracker Slip'"
			:buttonClass="'btn-sm btn-primary'">
			<x-slot:triggerButton>
				<i class="fa-solid fa-qrcode"></i>
				View Tracker Slip
			</x-slot:triggerButton>
			@include('application.partials.codes', [
				'application' => $application,
				'qrCode' => $qrCode,
				'barcode' => $barcode,
			])
			<x-slot:footer>
				<button type="button"
					class="btn btn-sm btn-light"
					data-bs-dismiss="modal">
					<i class="fa-solid fa-xmark"></i>
					Close
				</button>
				<a href="{{ route('application.tracker-slip', $application) }}"
					target="_blank"
					class="btn btn-sm btn-primary"
					role="button">
					<i class="fa-solid fa-print"></i>
					Print
				</a>
			</x-slot:footer>
		</x-modal>
	</x-slot:footer>
</x-card>
