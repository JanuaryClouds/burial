<div class="d-flex flex-column gap-4">
    @if(auth()->id() === $user->id)
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
				label="Email Address" :readonly="true"
				:required="true" />
		</div>
		<div class="col-12 col-md-6">
			<x-form.input wire:model='form.contactNumber'
				name="form.contactNumber"
				label="Contact Number"
				:required="true" />
		</div>
	</div>

    @else
        <p>Only the account owner can edit their profile. You can manage account status and roles.</p>
    @endif
	@role('superadmin')
		<x-form.check wire:model='form.isActive'
			name="form.isActive" :disabled="auth()->id() === $user->id"
			:checked="$user->is_active"
			label="Active" />
		<div class="row">
			@foreach ($roleOptions as $roleKey => $roleLabel)
				<div class="col-12 col-md-6 col-lg-4">
					<x-form.check wire:model='form.roles.{{ $roleKey }}'
						:name="'form.roles.' . $roleKey"
						wire:key='{{ $roleKey }}'
						:value="$roleKey"
						:checked="in_array($roleKey, $user->roles->pluck('name')->toArray())"
						:label="$roleLabel" />
				</div>
			@endforeach
		</div>
        @if(auth()->id() !== $user->id && blank($user->citizen_uuid) && $user->isInternal())
            <label class="form-label" for="account-mfa">Fresh authenticator code</label>
            <input id="account-mfa" class="form-control" wire:model="mfaCode" inputmode="numeric" autocomplete="one-time-code" maxlength="6">
            @error('mfa_code') <p class="text-danger">{{ $message }}</p> @enderror
            <p>Disabling an internal account or changing its superadmin role requires a new code. A code used at login cannot be reused.</p>
            @if($user->hasRole('superadmin'))
                <button type="button" class="btn btn-danger" wire:loading.attr="disabled" wire:target="changeSuperadmin"
                    data-superadmin-grant="false">Remove superadmin</button>
            @elseif($user->is_active)
                <button type="button" class="btn btn-warning" wire:loading.attr="disabled" wire:target="changeSuperadmin"
                    data-superadmin-grant="true">Grant superadmin</button>
            @endif
        @endif
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
