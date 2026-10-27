<div class="d-flex flex-column gap-4">
	<div class="row">
		<div class="col-12 col-md-8 col-lg-4">
			<x-form.input wire:model='form.firstName'
				name="form.firstName"
				label="First Name"
				:required="true" />
		</div>
		<div class="col-12 col-md-4 col-lg-3">
			<x-form.input wire:model='form.middleName'
				name="form.middleName"
				label="Middle Name" />
		</div>
		<div class="col-12 col-md-8 col-lg-3">
			<x-form.input wire:model='form.lastName'
				name="form.lastName"
				label="Last Name"
				:required="true" />
		</div>
		<div class="col-12 col-md-4 col-lg-2">
			<x-form.input wire:model='form.suffix'
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
			<x-form.input wire:model='form.contactNumber'
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
	<p class="text-muted">Every CMS-created user receives staff membership. Assign CSWDO and operational roles explicitly.</p>
    <div class="row">
        @foreach ($this->roleOptions as $roleName)
            <div class="col-md-4"><x-form.check wire:model="form.roles.{{ $roleName }}" :name="'form.roles.'.$roleName" :label="$roleName" :value="$roleName" /></div>
        @endforeach
    </div>
    @error('roles') <p class="text-danger">{{ $message }}</p> @enderror
	<div class="d-flex justify-content-end align-items-center gap-4">
		<x-button wire:click='clearForm'
			class="btn-sm btn-light">
			<x-icon.font-awesome class="fa-xmark" />
			Clear
		</x-button>
		<x-button wire:click='save'
			wire:loading.attr='disabled'
			class="btn-sm btn-success">
			<x-icon.font-awesome class="fa-floppy-disk" />
			Save
		</x-button>
	</div>
</div>
