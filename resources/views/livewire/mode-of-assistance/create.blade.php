<div class="d-flex flex-column gap-6">
	<x-form.input wire:model.live.blur='form.name'
		name="form.name"
		label="Name of Mode Of Assistance"
		:required="true" />
	<div class="d-flex justify-content-end align-items-center gap-4">
		<x-button wire:click='clearForm'
			class="btn-sm btn-light">
			<x-icon.font-awesome class="fa-xmark" />
			Clear
		</x-button>
		<x-button wire:click='save'
			class="btn-sm btn-success">
			<x-icon.font-awesome class="fa-floppy-disk" />
			Save
		</x-button>
	</div>
</div>
