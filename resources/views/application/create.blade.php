@extends('layouts.app')
@section('content')
	<form action="{{ route('application.store') }}"
		method="POST"
		id="applicationForm">
		@csrf
		<div class="row gap-y-2 gap-lg-0">
			<div class="col-12 col-md-12 col-lg-6">
				<x-card>
					<x-slot:header>Select a Client Record</x-slot:header>
					<x-form.select name="client_uuid"
						id="client_uuid_select"
						label="Draft Client Records"
						:required="true"
						:selected="session('client_uuid') ?? null"
						:options="$clientOptions ?? []" />
					<x-form.select name="relationship_id"
						label="Relationship to Beneficiary"
						:required="true"
						:options="$relationships ?? []" />
				</x-card>
			</div>
			<div class="col-12 col-md-12 col-lg-6">
				<x-card>
					<x-slot:header>Select a Beneficiary Record</x-slot:header>
					<x-form.select name="beneficiary_uuid"
						id="beneficiary_uuid_select"
						label="Draft Beneficiary Records"
						:required="true"
						:selected="session('beneficiary_uuid') ?? null"
						:options="$beneficiaryOptions ?? []" />
				</x-card>
			</div>
		</div>
	</form>

	<livewire:client.display :draftClients="$draftClients ?? []"
		:uuid="session('client_uuid') ?? null" />
	<livewire:beneficiary.display :draftBeneficiaries="$draftBeneficiaries ?? []"
		:uuid="session('beneficiary_uuid') ?? null" />

	{{-- TODO add images --}}

	<x-card>
		<div class="d-flex justify-content-end align-items-center gap-2">
			<x-modal modalId="confirmModal"
				buttonClass="btn btn-success btn-sm"
				modalTitle="Confirm Application Submission"
				modalSize="lg">
				<x-slot:triggerButton>
					<i class="fa-solid fa-check"></i>
					Submit Application
				</x-slot:triggerButton>
				<p class="fs-4">Are you sure you want to submit all of these information into your application. Once submitted,
					editing will be disabled</p>
				<x-slot:footer>
					<button type="button"
						class="btn btn-secondary"
						data-bs-dismiss="modal">
						<i class="fa-solid fa-xmark"></i>
						Cancel
					</button>
					<button type="submit"
						form="applicationForm"
						class="btn btn-success">
						<i class="fa-solid fa-check"></i>
						Submit
					</button>
				</x-slot:footer>
			</x-modal>
		</div>
	</x-card>
@endsection
