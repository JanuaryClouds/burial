<div>
	<div class="row">
		<div class="col-12 d-flex flex-column gap-4">
			<x-form.input wire:model='form.referred_to'
				name="form.referred_to"
				label="Referred to"
				:required="true" />
			<x-form.textarea wire:model='form.reason'
				name="form.reason"
				label="Reason"
				:required="true" />
		</div>
		<div class="col-12 d-flex justify-content-end gap-2">
			<x-modal :modalId="'confirmReferralModal'"
				:modalSize="'md'"
				:modalTitle="'Confirm Referral'"
				:buttonClass="'btn-sm btn-danger'">
				<x-slot:triggerButton>
					<x-icon.font-awesome class="fa-forward" />
					Refer this Application
				</x-slot:triggerButton>
				<x-slot:header>
					Confirm Referral
				</x-slot:header>
				<p class="fs-4">
					Are you sure you want to refer this application to <span class="fw-bold">{{ $form->referred_to }}</span>?
				</p>
				<x-slot:footer>
					<x-button wire:click="save"
						class="btn-sm btn-danger">
						<x-icon.font-awesome class="fa-exclamation-triangle" />
						Refer
					</x-button>
				</x-slot:footer>
			</x-modal>
		</div>
	</div>
</div>
