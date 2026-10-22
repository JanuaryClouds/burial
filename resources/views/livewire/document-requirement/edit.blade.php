<div class="d-flex flex-column gap-4">
	<x-card>
		<x-slot:header>
			Edit {{ $name }}
		</x-slot:header>
		<div class="row">
			<div class="col-12 col-md-6">
				<x-form.input wire:model.live.blur='name'
					name="name"
					label="Document Name"
					:required="true" />
			</div>
			<div class="col-12 col-md-6">
				<x-form.input wire:model='source'
					name="source"
					label="Source of Document"
					:required="true" />
			</div>
		</div>
		<x-form.textarea wire:model='description'
			name="description"
			label="Description"
			:required="false" />
		<div class="row">
			<div class="col-4 col-lg-3">
				<x-form.check wire:model='isRequired'
					name="isRequired"
					label="Required" />
			</div>
			<div class="col-4 col-lg-3">
				<x-form.check wire:model='forMuslim'
					name="forMuslim"
					label="For Muslim" />
			</div>
			<div class="col-4 col-lg-3">
				<x-form.check wire:model='isActive'
					name="isActive"
					label="Active" />
			</div>
		</div>
		<div class="d-flex justify-content-end align-items-center gap-2">
			<a href="{{ route('document_requirements.index') }}"
				class="btn btn-sm btn-light">
				<x-icon.font-awesome class="fa-xmark" />
				Cancel
			</a>
			<x-button wire:click='update'
				wire:loading.attr='disabled'
				class="btn-sm btn-success">
				<x-icon.font-awesome class="fa-floppy-disk" />
				Update
			</x-button>
		</div>
	</x-card>
</div>
