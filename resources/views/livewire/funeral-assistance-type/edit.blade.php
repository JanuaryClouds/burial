<div class="d-flex flex-column gap-6">
	<x-form.input wire:model.live.blur='form.name'
		name="form.name"
		label="Name of New Type of Funeral Assistance"
		:required="true" />
	<x-form.textarea wire:model.live.blur="form.description"
		name="form.description"
		label="Description"
		:required="true" />
	<x-form.select wire:model.live='form.workflowUuid'
		name="form.workflowUuid"
		label="Workflow"
		:options="$workflows" />
	<div class="d-flex justify-content-end align-items-center gap-4">
		<a href="{{ route('funeral_assistance_types.index') }}"
			class="btn btn-sm btn-light">
			<x-icon.font-awesome class="fa-arrow-right" />
			Back
		</a>
		<x-button wire:click='save'
			class="btn-sm btn-success">
			<x-icon.font-awesome class="fa-floppy-disk" />
			Save
		</x-button>
	</div>
</div>
