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
	@if ($clientUuid && $beneficiaryUuid && $relationshipId && count($images) !== 0)
		<p>
			Please tick checkbox below to confirm that you have read and understood the terms and conditions and that you agree
			to the
			terms and conditions.
		</p>
		<x-form.check wire:model.live='agreedToTerms'
			name="agreedToTerms"
			id="agreedToTerms">
			<x-slot:label>
				I have read and understood the <a href="#">terms and conditions</a>.
			</x-slot:label>
		</x-form.check>
	@else
		<x-callout class="border-danger-subtle bg-danger-subtle">
			<x-slot:icon>
				<x-icon.font-awesome class="fa-exclamation-circle text-danger fs-2" />
			</x-slot:icon>
			<x-slot:title>
				<p class="text-danger fs-6 fw-bold">
					Incomplete Information
				</p>
			</x-slot:title>
			<p class="text-danger fw-semibold">
				Please provide the following before submitting:
			</p>
			<ul class="text-danger list-unstyled">
				@if (!$clientUuid)
					<li>
						<x-icon.font-awesome class="fa-xmark" />
						Client
					</li>
				@endif
				@if (!$beneficiaryUuid)
					<li>
						<x-icon.font-awesome class="fa-xmark" />
						Beneficiary
					</li>
				@endif
				@if (!$relationshipId)
					<li>
						<x-icon.font-awesome class="fa-xmark" />
						Relationship
					</li>
				@endif
				@if (count($images) == 0)
					<li>
						<x-icon.font-awesome class="fa-xmark" />
						Documents
					</li>
				@endif
			</ul>
		</x-callout>
	@endif
	<x-slot:footer>
		@if ($clientUuid || $beneficiaryUuid || $relationshipId)
			<x-button wire:click="clearForm"
				class="btn-sm btn-danger">
				<x-icon.font-awesome class="fa-xmark" />
				Clear Form
			</x-button>
		@endif
		@if ($agreedToTerms)
			<x-button wire:click="save"
				wire:loading.attribute="disabled"
				class="btn-sm btn-success">
				<x-icon.font-awesome class="fa-floppy-disk" />
				Submit
			</x-button>
		@endif
	</x-slot:footer>
</x-card>
