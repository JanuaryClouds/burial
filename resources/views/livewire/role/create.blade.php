<div class="d-flex flex-column gap-6">
	<x-form.input wire:model.live.blur='form.name'
		name="form.name"
		label="Role Name"
		:required="true" />
	<x-form.select wire:model='form.permissions'
		wire:ignore
		name="form.permission"
		label="Permissions"
		:options="$permissions"
		:multiple="true" />
	<div class="d-flex justify-content-end align-items-center gap-6">
		<x-button wire:click='clearForm'
			class="btn-sm btn-light">
			<x-icon.font-awesome class="fa-xmark" />
			Cancel
		</x-button>
		<x-button class="btn-sm btn-primary"
			wire:click='save'>
			<x-icon.font-awesome class="fa-floppy-disk" />
			Save
		</x-button>
	</div>
</div>
