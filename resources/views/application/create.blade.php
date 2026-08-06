@extends('layouts.app')
@section('content')
	<form action="{{ route('application.store') }}"
		method="POST"
		class="d-flex flex-column gap-8"
		id="applicationForm"
		enctype="multipart/form-data">
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
						id="relationship_select"
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

		<x-card>
			<x-slot:header>
				Submitted Documents
			</x-slot:header>
			<div class="alert alert-warning mb-3"
				role="alert">
				<i class="fa-solid fa-info-circle me-2"></i><strong>Note:</strong>
				During the interview, please bring hard copies of the documents you have submitted as soft copies.
			</div>
			@include('application.partials.documents')
		</x-card>
	</form>

	<livewire:client.display :draftClients="$draftClients ?? []"
		:uuid="session('client_uuid') ?? null" />
	<livewire:beneficiary.display :draftBeneficiaries="$draftBeneficiaries ?? []"
		:uuid="session('beneficiary_uuid') ?? null" />

	<x-card>
		<div class="d-flex justify-content-end align-items-center gap-2">
			<button type="button"
				class="btn btn-sm btn-light"
				id="disabledSaveButton"
				data-bs-toggle="tooltip"
				data-bs-placement="top"
				title="Complete the application form first">
				<i class="fa-solid fa-floppy-disk"></i>
				Submit Application
			</button>

			<div id="saveButton">
				<x-modal modalId="confirmModal"
					buttonClass="btn btn-success btn-sm"
					modalTitle="Confirm Application Submission"
					modalSize="lg">
					<x-slot:triggerButton>
						<i class="fa-solid fa-floppy-disk"></i>
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
		</div>
	</x-card>
	<script nonce="{{ $nonce ?? '' }}">
		$(document).ready(function() {
			$('#saveButton').hide();
			$('#disabledSaveButton').show();
			let client_uuid = $('#client_uuid_select').find('option:selected').val().trim();
			let beneficiary_uuid = $('#beneficiary_uuid_select').find('option:selected').val().trim();
			let relationship_id = $('#relationship_select').find('option:selected').val().trim();

			$('#client_uuid_select').on('change', function() {
				client_uuid = $(this).find('option:selected').val().trim();
				validateForm();
			});

			$('#beneficiary_uuid_select').on('change', function() {
				beneficiary_uuid = $(this).find('option:selected').val().trim();
				validateForm();
			});

			$('#relationship_select').on('change', function() {
				relationship_id = $(this).find('option:selected').val().trim();
				validateForm();
			});

			function validateForm() {
				if (client_uuid !== '' && beneficiary_uuid !== '' && relationship_id !== '') {
					$('#saveButton').show();
					$('#disabledSaveButton').hide();
				} else {
					$('#saveButton').hide();
					$('#disabledSaveButton').show();
				}
			};

			validateForm();
		});
	</script>
@endsection
