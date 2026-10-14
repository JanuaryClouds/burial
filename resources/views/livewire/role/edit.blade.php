<div class="d-flex flex-column gap-6">
	<x-form.input wire:model.live.blur='name'
		name="name"
		label="Role Name"
		:required="true" />
	<div class="row">
		@foreach ($permissionsOptions as $permissionKey => $permissionLabel)
			<div class="col-12 col-lg-4">
				<x-form.check :name="'permissions.' . $permissionKey"
					wire:model="permissions.{{ $permissionKey }}"
					wire:key="{{ $permissionKey }}"
					:value="$permissionKey"
					:checked="in_array($permissionKey, $role->permissions->pluck('name')->toArray())"
					:label="$permissionLabel" />
			</div>
		@endforeach
	</div>
	<div class="d-flex justify-content-end align-items-center gap-4">
		<x-button wire:click="clearPermissions"
			class="btn-sm btn-light">
			<x-icon.font-awesome class="fa-circle-xmark" />
			Clear Permissions
		</x-button>
		<a href="{{ route('role.index') }}"
			class="btn btn-sm btn-light">
			<x-icon.font-awesome class="fa-xmark" />
			Cancel
		</a>
		<x-button class="btn-sm btn-success"
			wire:click='save'>
			<x-icon.font-awesome class="fa-floppy-disk" />
			Save
		</x-button>
	</div>
</div>
