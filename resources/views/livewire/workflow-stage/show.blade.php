<x-card class="shadow-xl">
	<form wire:submit='save({{ $stage->uuid }})'
		method="POST">
		@csrf
		<div class="row">
			<div class="col-12">
				<x-form.input name="name"
					label="Name of Stage"
					wire:model.live.blur='name'
					required />
			</div>
			<div class="col-12">
				<x-form.textarea name="description"
					label="Description of Stage"
					wire:model.live.blur='description'
					required />
			</div>
		</div>
		<x-slot:footer>
			@if ($stage->incomingStages->isNotEmpty())
				<x-button class="btn-light"
					wire:click="moveUp('{{ $stage->uuid }}')"
					wire:loading.attr='disabled'>
					<i class="fa-solid fa-arrow-up"></i>
					Move Up
				</x-button>
			@endif
			@if ($stage->outgoingStages->isNotEmpty())
				<x-button class="btn-light"
					wire:click="moveDown('{{ $stage->uuid }}')"
					wire:loading.attr='disabled'>
					<i class="fa-solid fa-arrow-down"></i>
					Move Down
				</x-button>
			@endif
			<x-button class="btn-success"
				wire:click="save('{{ $stage->uuid }}')"
				wire:loading.attr='disabled'>
				<i class="fa-solid fa-floppy-disk"
					wire:loading.remove></i>
				<span wire:loading.remove>Save</span>
			</x-button>
		</x-slot:footer>
	</form>
</x-card>
