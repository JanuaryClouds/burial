<div class="d-flex flex-column gap-2">
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
