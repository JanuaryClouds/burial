<div class="d-flex flex-column gap-4">
	<x-form.textarea wire:model='form.reason'
		name="form.reason"
		label="Reason to Close Application"
		:required="true" />
	<div class="d-flex justify-content-end">
		<x-button wire:click='save'
			wire:loading.attr='disabled'
			class="btn-sm btn-success">
			<x-icon.font-awesome class="fa-floppy-disk" />
			Submit
		</x-button>
	</div>
</div>
