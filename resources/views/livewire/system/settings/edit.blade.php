<div class="d-flex flex-column gap-2">
    <x-form.check wire:model="form.walkInIntakeEnabled" name="walkInIntakeEnabled"
        :checked="$form->walkInIntakeEnabled" label="Enable Walk-in Intake" />
    <p class="text-muted">Allow authorized staff to record walk-in applicants and citizens to link their walk-in records. When disabled, walk-in pages and actions are unavailable. Existing application records and processing history are preserved.</p>
    @error('form.walkInIntakeEnabled') <p class="text-danger">{{ $message }}</p> @enderror
    <label class="form-label" for="claimant-limit">Claimant changes allowed per application (0 disables changes)</label>
    <input id="claimant-limit" class="form-control" type="number" min="0" step="1" wire:model="form.claimantChangeLimit">
    @error('form.claimantChangeLimit') <p class="text-danger">{{ $message }}</p> @enderror
	{{-- start::Social Welfare Officer --}}
	<x-form.input wire:model='form.socialWelfareOfficer'
		name="socialWelfareOfficer"
		label="Social Welfare Officer"
		placeholder="Enter social welfare officer"
		:value="$settings->social_welfare_officer"
		:required="true" />
	{{-- end::Social Welfare Officer --}}

	{{-- start::Department Head --}}
	<x-form.input wire:model='form.deptHead'
		name="deptHead"
		label="Department Head"
		:value="$settings->department_head"
		:required="true" />
	{{-- end::Department Head --}}

	{{-- start:Department Email --}}
	<x-form.input wire:model='form.deptEmail'
		name="deptEmail"
		label="Department Email"
		placeholder="Enter department email"
		:value="$settings->department_email"
		:required="true" />
	{{-- end:Department Email --}}

	{{-- start::Maintenane Mode --}}
	<x-form.check wire:model='form.maintenanceMode'
		name="maintenanceMode"
		:checked="$settings->maintenance_mode"
		label="Maintenance Mode" />
	{{-- end::Maintenane Mode --}}

    <label class="form-label" for="settings-mfa">Fresh authenticator code</label>
    <input id="settings-mfa" class="form-control" wire:model="mfaCode" inputmode="numeric" autocomplete="one-time-code" maxlength="6">
    @error('mfa_code') <p class="text-danger">{{ $message }}</p> @enderror
    <p>Every settings save requires a new code. If you just used this code, wait for the next one.</p>
	<div class="d-flex justify-content-end align-items-center gap-2">
		<a href={{ route('system.index') }}
			class="btn btn-sm btn-light">
			<x-icon.font-awesome class="fa-external-link" />
			Back
		</a>
		<x-button wire:click='save'
			class="btn btn-sm btn-warning">
			<x-icon.font-awesome class="fa-save" />
			Update
		</x-button>
	</div>
</div>
