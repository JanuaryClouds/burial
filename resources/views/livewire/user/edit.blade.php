<div class="d-flex flex-column gap-4">
	<div class="row">
		<div class="col-12 col-md-8 col-lg-4">
			<x-form.input wire:model.live.blur='form.firstName'
				name="form.firstName"
				label="First Name"
				:required="true" />
		</div>
		<div class="col-12 col-md-4 col-lg-3">
			<x-form.input wire:model.live.blur='form.middleName'
				name="form.middleName"
				label="Middle Name" />
		</div>
		<div class="col-12 col-md-8 col-lg-3">
			<x-form.input wire:model.live.blur='form.lastName'
				name="form.lastName"
				label="Last Name"
				:required="true" />
		</div>
		<div class="col-12 col-md-4 col-lg-2">
			<x-form.input wire:model.live.blur='form.suffix'
				name="form.suffix"
				label="Suffix" />
		</div>
	</div>
	<div class="row">
		<div class="col-12 col-md-6">
			<x-form.input wire:model.live.blur='form.email'
				name="form.email"
				label="Email Address"
				:required="true" />
		</div>
		<div class="col-12 col-md-6">
			<x-form.input wire:model.live.blur='form.contactNumber'
				name="form.contactNumber"
				label="Contact Number"
				:required="true" />
		</div>
	</div>
	<div class="row">
		<div class="col-12 col-md-6">
			<x-form.input wire:model.live.blur='form.password'
				name="form.password"
				label="Password"
				type="password"
				:required="true" />
		</div>
		<div class="col-12 col-md-6">
			<x-form.input wire:model.live.blur='form.confirmPassword'
				name="form.confirmPassword"
				label="Confirm Password"
				type="password"
				:required="true" />
		</div>
	</div>
	@role('superadmin')
		<x-form.select wire:model.live='form.roleIds'
			name="form.roleIds"
			label="Roles"
			:required="true"
			:options="$roles"
			:selected="$user->roles->pluck('id')->toArray()"
			:multiple="true" />
	@endrole
	<div class="d-flex justify-content-end align-items-center gap-4">
		@role('superadmin')
			<a href="{{ route('user.index') }}"
				class="btn btn-sm btn-light">
				<x-icon.font-awesome class="fa-xmark" />
				Cancel
			</a>
		@else
			<a href="{{ back()->getTargetUrl() }}"
				class="btn btn-sm btn-light">
				<x-icon.font-awesome class="fa-xmark" />
				Cancel
			</a>
		@endrole
		<x-button wire:click='save'
			class="btn-sm btn-success">
			<x-icon.font-awesome class="fa-floppy-disk" />
			Save
		</x-button>
	</div>
</div>
