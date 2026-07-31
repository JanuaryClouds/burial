<x-card>
	<x-slot:header>
		<i class="fa-solid fa-user-injured text-primary me-2"></i>
		Select Beneficiary Record
	</x-slot:header>

	@if (isset($beneficiaries) && count($beneficiaries) > 0)
		<div class="row">
			<div class="col-12 col-md-6">
				<x-form.select name="beneficiary_uuid"
					id="beneficiary_uuid"
					label="Choose a Draft Beneficiary"
					wire:model.live="selectedBeneficiaryUuid"
					:options="$beneficiaries ?? []" />
			</div>
		</div>

		@if ($selectedBeneficiary)
			<div class="separator my-4"></div>
			<div class="alert alert-info d-flex align-items-center">
				<i class="fa-solid fa-circle-check text-success me-3 fs-4"></i>
				<div>
					<strong>Beneficiary Selected:</strong>
					{{ $selectedBeneficiary->fullname() }}
				</div>
			</div>

			<div class="row mt-3">
				<div class="col-12 col-md-4 col-lg-3">
					<label class="form-label text-gray-600 fw-bold">Sex</label>
					<p class="fw-bolder text-gray-800">{{ $selectedBeneficiary->sex?->name ?? 'N/A' }}</p>
				</div>
				<div class="col-12 col-md-4 col-lg-3">
					<label class="form-label text-gray-600 fw-bold">Date of Birth</label>
					<p class="fw-bolder text-gray-800">{{ $selectedBeneficiary->date_of_birth ? \Carbon\Carbon::parse($selectedBeneficiary->date_of_birth)->format('F d, Y') : 'N/A' }}</p>
				</div>
				<div class="col-12 col-md-4 col-lg-3">
					<label class="form-label text-gray-600 fw-bold">Date of Death</label>
					<p class="fw-bolder text-gray-800">{{ $selectedBeneficiary->date_of_death ? \Carbon\Carbon::parse($selectedBeneficiary->date_of_death)->format('F d, Y') : 'N/A' }}</p>
				</div>
				<div class="col-12 col-md-4 col-lg-3">
					<label class="form-label text-gray-600 fw-bold">Religion</label>
					<p class="fw-bolder text-gray-800">{{ $selectedBeneficiary->religion?->name ?? 'N/A' }}</p>
				</div>
				<div class="col-12 col-md-6 col-lg-4">
					<label class="form-label text-gray-600 fw-bold">Place of Birth</label>
					<p class="fw-bolder text-gray-800">{{ $selectedBeneficiary->place_of_birth ?? 'N/A' }}</p>
				</div>
				<div class="col-12 col-md-6 col-lg-4">
					<label class="form-label text-gray-600 fw-bold">Barangay</label>
					<p class="fw-bolder text-gray-800">{{ $selectedBeneficiary->barangay?->name ?? 'N/A' }}</p>
				</div>
			</div>

			@if ($selectedBeneficiary->family && $selectedBeneficiary->family->count() > 0)
				<div class="separator my-4"></div>
				<h6 class="text-primary fw-bold mb-3">Family Composition</h6>
				<div class="table-responsive">
					<table class="table align-middle table-row-dashed fs-6 gy-3">
						<thead>
							<tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
								<th>Name</th>
								<th>Relationship</th>
								<th>Sex</th>
								<th>Age</th>
								<th>Civil Status</th>
								<th>Occupation</th>
								<th>Income</th>
							</tr>
						</thead>
						<tbody class="text-gray-600 fw-semibold">
							@foreach ($selectedBeneficiary->family as $member)
								<tr>
									<td>{{ $member->name }}</td>
									<td>{{ $member->relationship?->name ?? 'N/A' }}</td>
									<td>{{ $member->sex?->name ?? 'N/A' }}</td>
									<td>{{ $member->age }}</td>
									<td>{{ $member->civil?->name ?? 'N/A' }}</td>
									<td>{{ $member->occupation ?? 'N/A' }}</td>
									<td>{{ $member->income ?? 'N/A' }}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			@endif
		@endif
	@else
		<div class="text-center py-6">
			<i class="fa-solid fa-inbox text-muted fs-3x mb-3 d-block"></i>
			<p class="text-muted fs-5">You don't have any draft beneficiary records yet.</p>
			<a href="{{ route('beneficiary.create') }}" class="btn btn-primary">
				<i class="fa-solid fa-plus me-2"></i>
				Create New Beneficiary Record
			</a>
		</div>
	@endif

	<x-slot:footer>
		<small class="text-muted">
			<i class="fa-solid fa-info-circle me-1"></i>
			You can create a new beneficiary record if none of the draft records are suitable.
		</small>
		<a href="{{ route('beneficiary.create') }}" class="btn btn-light btn-sm">
			<i class="fa-solid fa-plus"></i>
			New Beneficiary
		</a>
	</x-slot:footer>
</x-card>
