<div class="d-flex flex-column gap-3"
	wire:ignore>
	<x-form.textarea wire:model='form.reason'
		name="form.reason"
		label="Reason for Rejection"
		:required="true" />

	<div class="d-flex justify-content-end">
		<x-modal :modalId="'confirm-rejection'"
			:modalTitle="'Confirm Rejection'"
			:modalSize="'md'"
			:buttonClass="'btn btn-sm btn-danger'">
			<x-slot:triggerButton>
				<x-icon.font-awesome class="fa-xmark-circle" />
				Reject Application
			</x-slot:triggerButton>
			<p class="fs-4">Are you sure you want to reject this application? This action cannot be
				undone.</p>
			<x-slot:footer>
				<x-button wire:click="save"
					wire:loading.attr="disabled"
					class="btn btn-sm btn-danger">
					<x-icon.font-awesome class="fa-xmark-circle" />
					Reject Application
				</x-button>
			</x-slot:footer>
		</x-modal>
	</div>
</div>
