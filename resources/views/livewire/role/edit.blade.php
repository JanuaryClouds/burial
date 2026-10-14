<div class="d-flex flex-column gap-6">
	<x-form.input wire:model.live.blur='form.name'
		name="form.name"
		label="Role Name"
		:required="true" />
	<x-form.select wire:model='form.permissions'
		name="form.permission"
		label="Permissions"
		:options="$permissions"
		:selected="$form->permissions"
		:multiple="true" />
	<div class="d-flex justify-content-end align-items-center gap-6">
		<a href="{{ route('role.index') }}"
			class="btn btn-sm btn-light">
			<x-icon.font-awesome class="fa-xmark" />
			Cancel
		</a>
		<button class="btn btn-primary"
			wire:click='save'>
			Save
		</button>
	</div>
</div>
