<div class="d-flex flex-column gap-4">
	<x-form.input wire:model='form.name'
		name="form.name"
		label="Name of New Workflow"
		:required="true" />
	<x-form.input wire:model='form.description'
		name="form.description"
		label="Description"
		:required="true" />
	<div class="d-flex justify-content-end align-items-center gap-2">
		<x-button wire:click='clear'
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
