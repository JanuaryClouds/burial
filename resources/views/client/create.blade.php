@extends('layouts.app')
@section('content')
	<div class="row gap-2 gap-md-0">
		<div class="col-12 col-md-6">
			<x-card>
				<x-slot:header>
					<i class="fa-solid fa-info-circle fs-3 me-2"></i>
					Notice
				</x-slot:header>
				<p class="fs-5">
					This will create a client record upon submission. You can continue providing details regarding the
					deceased/beneficiary after submission
				</p>
			</x-card>
		</div>
		<div class="col-12 col-md-6">
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
		<form action="{{ route('client.store') }}"
			method="POST"
			id="createClientForm">
			@csrf
			<x-slot:header>Your Basic Information</x-slot:header>
			@include('client.partials.create.form')
			<x-slot:footer>
				<a name=""
					id=""
					class="btn btn-light"
					href="{{ route('client.create') }}"
					role="button">
					<i class="fa-solid fa-xmark"></i>
					Cancel
				</a>
				<x-modal modalId="confirmSubmissionModal"
					modalSize="md"
					modalTitle="Confirm Details"
					buttonClass="btn-success">
					<x-slot:triggerButton>
						<i class="fa-solid fa-floppy-disk"></i>
						Submit
					</x-slot:triggerButton>
					<p class="fs-4">Are you sure you want to submit the folowing details?</p>
					<x-slot:footer>
						<button type="button"
							class="btn btn-light"
							data-bs-dismiss="modal">
							<i class="fa-solid fa-xmark"></i>
							Close
						</button>
						<button type="submit"
							form="createClientForm"
							class="btn btn-success"
							id="submitBtn">
							<i class="fa-solid fa-check"></i>
							Confirm
						</button>
					</x-slot:footer>
				</x-modal>
			</x-slot:footer>
		</form>
	</x-card>
@endsection
