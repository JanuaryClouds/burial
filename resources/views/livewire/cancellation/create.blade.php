<div class="d-flex flex-column gap-6">
	{{-- start::Form --}}
	<div class="row">
		<div class="col-12">
			<x-form.textarea label="Reason"
				name="form.reason"
				wire:model="form.reason"
				placeholder="Reason for cancellation"
				:required="true" />
		</div>
		<div class="col-12 d-flex justify-content-end gap-2">
			<x-modal :modalId="'confirmCancellationModal'"
				:modalSize="'md'"
				:buttonClass="'btn-sm btn-danger'"
				:modalTitle="'Confirm Cancellation of Application'">
				<x-slot:triggerButton>
					<x-icon.font-awesome class="fa-stop" />
					Cancel Application
				</x-slot:triggerButton>
				<div class="d-flex flex-column gap-2">
					<p class="fs-4">Are you sure you want to cancel this application?</p>
				</div>
				<x-slot:footer>
					<x-button wire:click="save"
						class="btn-sm btn-danger">
						<x-icon.font-awesome class="fa-stop" />
						Cancel Application
					</x-button>
				</x-slot:footer>
			</x-modal>
		</div>
	</div>
	{{-- end::Form --}}
</div>
