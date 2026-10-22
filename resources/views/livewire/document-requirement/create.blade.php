<div class="d-flex flex-column gap-4">
	<div class="row">
		<div class="col-12 col-md-6">
			<x-form.input wire:model.live.blur='form.name'
				name="form.name"
				label="Name of Document"
				:required="true" />
		</div>
		<div class="col-12 col-md-6">
			<x-form.input wire:model='form.source'
				name="form.source"
				label="Source of Document"
				:required="true" />
		</div>
	</div>
	<div>
		<x-form.textarea wire:model='form.description'
			name="form.description"
			label="Description"
			:required="true" />
	</div>
	<div class="row">
		<div class="col-6 col-md-4 col-lg-3">
			<x-form.check wire:model='form.isRequired'
				name="form.isRequired"
				label="Required" />
		</div>
		<div class="col-6 col-md-4 col-lg-3">
			<x-form.check wire:model='form.forMuslim'
				name="form.forMuslim"
				label="For Muslim" />
		</div>
	</div>
	<div class="d-flex justify-content-end align-items-center gap-2">
		<x-button wire:click='save'
			wire:loading.attr='disabled'
			class="btn-sm btn-success">
			<x-icon.font-awesome class="fa-floppy-disk" />
			Save
		</x-button>
	</div>
</div>
