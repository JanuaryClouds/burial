<div class="d-flex flex-column gap-6">
	<div class="row">
		<div class="col-12 col-lg-6">
			<x-form.input wire:model.live.blur='form.name'
				name="form.name"
				label="Name of New Type of Funeral Assistance"
				:required="true" />
		</div>
		<div class="col-12 col-lg-6">
			<x-form.select wire:model.live='form.workflowUuid'
				name="form.workflowUuid"
				label="Workflow"
				:options="$workflows"
                :selected="$form->workflowUuid" />
		</div>
	</div>
	<x-form.textarea wire:model.live.blur="form.description"
		name="form.description"
		label="Description"
		:required="true" />
	<div class="d-flex justify-content-end align-items-center gap-4">
		<x-button wire:click='clearForm'
			class="btn-sm btn-light">
			<x-icon.font-awesome class="fa-xmark" />
			Clear
		</x-button>
		<x-button wire:click='save'
			class="btn-sm btn-success">
			<x-icon.font-awesome class="fa-floppy-disk" />
			Save
		</x-button>
	</div>
</div>
