<div class="d-flex flex-column gap-6">
	<x-form.input wire:model.live.blur='form.name'
		name="form.name"
		label="Role Name"
		:required="true" />
	<x-form.select wire:model.live='form.permissions'
		name="form.permission"
		label="Permissions"
		:options="$permissions" />
	<div class="d-flex justify-content-end align-items-center gap-6">
		<button class="btn btn-primary"
			wire:click='save'>
			Save
		</button>
	</div>
</div>
