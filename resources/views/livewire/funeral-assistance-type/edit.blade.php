<div class="d-flex flex-column gap-6">
	<x-form.input wire:model.live.blur='form.name'
		name="form.name"
		label="Name of Funeral Assistance Type"
		:required="true" />
	<x-form.textarea wire:model.live.blur="form.description"
		name="form.description"
		label="Description"
		:required="true" />
    @if ($unavailableWorkflowName)
        <div class="alert alert-warning">
            The currently assigned workflow, <strong>{{ $unavailableWorkflowName }}</strong>, is unavailable for new recommendations (paused, superseded, or incomplete).
            Choose an available workflow version or clear the assignment before saving.
            <button type="button" class="btn btn-sm btn-light mt-2" wire:click="$set('form.workflowUuid', null)">Clear Workflow Assignment</button>
        </div>
    @endif
	<x-form.select wire:model.live='form.workflowUuid'
		name="form.workflowUuid"
		label="Workflow"
		:options="$workflows"
        :selected="$form->workflowUuid" />
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
