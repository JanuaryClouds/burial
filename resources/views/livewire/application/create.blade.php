<x-slot:page_title>Create Application</x-slot:page_title>
<x-slot:page_subtitle>Funeral Assistance System | CSWDO Taguig</x-slot:page_subtitle>
<div class="d-flex flex-column gap-6">
	@include('application.create.partials.client')
	@include('application.create.partials.beneficiary')
	@include('application.create.partials.relationship')
	@include('application.create.partials.documents')
	<x-card>
		<x-slot:header>Confirm Selected Data</x-slot:header>
		<p class="fw-bold">
			Please carefully double check and confirm you have selected the correct drafts. Once submitted, those drafts will not
			be available for editing.
		</p>
		<p class="text-danger">
			Photos of the documents you submitted will be used for verification. Submitting false documents and false information
			will automatically disqualify your application. Bringing the original copy of the submitted documents will be
			required for the interview and assessment.
		</p>
		<x-slot:footer>
			@if ($clientUuid || $beneficiaryUuid || $relationshipId)
				<x-button wire:click="clearForm"
					class="btn-sm btn-danger">
					<x-icon.font-awesome class="fa-xmark" />
					Clear Form
				</x-button>
			@endif
			@if ($clientUuid && $beneficiaryUuid && $relationshipId)
				<x-modal modalId="confirmSubmissionmodal"
					modalTitle="Confirm Submission"
					modalSize="md"
					buttonClass="btn-sm btn-success">
					<x-slot:triggerButton>
						<x-icon.font-awesome class="fa-floppy-disk" />
						Submit
					</x-slot:triggerButton>
					<p class="fw-bold">
						Are you sure you want to submit these drafts? Submitting these will determine eligibility for funeral
						assistance. They will also no longer be available for editing.
					</p>
					<x-slot:footer>
						<x-button wire:click="save"
							wire:loading.attribute="disabled"
							class="btn-sm btn-success">
							<x-icon.font-awesome class="fa-floppy-disk" />
							Submit
						</x-button>
					</x-slot:footer>
				</x-modal>
			@endif
		</x-slot:footer>
	</x-card>
</div>
