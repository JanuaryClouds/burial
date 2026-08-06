@extends('layouts.app')
@section('content')
	<div class="row gap-2 gap-md-0">
		<div class="col-12">
			<x-card>
				<x-slot:header>
					<i class="fa-solid fa-exclamation-triangle text-warning fs-3 me-2"></i>
					Warning
				</x-slot:header>
				<p class="fs-5 fw-bold">
					Please make sure to fill out fields marked with an asterisk (*) symbol. They are required information you should
					provide.
				</p>
			</x-card>
		</div>
	</div>
	<x-card>
		<form action="{{ route('beneficiary.store') }}"
			id="beneficiaryForm"
			method="POST">
			@csrf
			<x-slot:header>Beneficiary's Information</x-slot:header>
			@include('beneficiary.partials.create.form')
			<div class="separator my-4"></div>
			<h5 class="card-title">Family Composition</h5>
			@include('livewire.beneficiary.family-composition')
			<x-slot:footer>
				<a name=""
					id=""
					class="btn btn-light"
					href="{{ route('beneficiary.index') }}"
					role="button">
					<i class="fa-solid fa-xmark"></i>
					Cancel
				</a>
				<x-modal modalId="confirmSubmissionModal"
					modalSize="md"
					modalTitle="Confirm Submission Modal"
					buttonClass="btn-success">
					<x-slot:header>
						Confirm Submission
					</x-slot:header>
					<x-slot:triggerButton>
						<i class="fa-solid fa-floppy-disk"></i>
						Submit
					</x-slot:triggerButton>
					<p class="fs-5">Are you sure you want to submit?</p>
					<x-slot:footer>
						<button type="button"
							class="btn btn-secondary"
							data-bs-dismiss="modal">
							<i class="fa-solid fa-xmark"></i>
							Cancel
						</button>
						<button type="submit"
							form="beneficiaryForm"
							class="btn btn-success">
							<i class="fa-solid fa-check"></i>
							Confirm
						</button>
					</x-slot:footer>
				</x-modal>
			</x-slot:footer>
		</form>
	</x-card>
@endsection
