<x-card class="shadow-xl">
	<x-slot:header>
		Stage {{ $position }} {{ app()->hasDebugModeEnabled() ? '(' . $maxStages . ')' : '' }}
		<x-slot:toolbar>
			<div wire:loading>
				<div class="spinner-border text-success"
					role="status">
					<span class="visually-hidden">Loading...</span>
				</div>
			</div>
		</x-slot:toolbar>
	</x-slot:header>
	<form wire:submit='save()'
		method="POST">
		@csrf
		<div wire:loading.remove>
			<div class="row">
				<div class="col-12">
					<x-form.input name="name"
						label="Name of Stage"
						wire:model='name'
						required />
				</div>
				<div class="col-12">
					<x-form.textarea name="description"
						label="Description of Stage"
						wire:model='description'
						required />
				</div>
				<div class="col-12">
					<x-form.select name="permission_id"
						class="select-dynamic"
						:id="'permission_id_' . $stage->uuid"
						label="Permission to Proceed to this Stage"
						wire:model='permission_id'
						:options="$this->workflowPermissions"
						required
						:selected="$this->permission_id" />
				</div>
			</div>
		</div>
		<x-slot:footer>
			@if ($stage->trashed())
				<div wire:loading.remove>
					<x-button class="btn-sm btn-info"
						wire:click="restore"
						wire:loading.attr='disabled'>
						<i class="fa-solid fa-trash-arrow-up"></i>
						<span>Restore</span>
					</x-button>
				</div>
			@else
				@if ($position > 1)
					<div wire:loading.remove>
						<x-button class="btn-sm btn-light"
							wire:click="moveUp"
							wire:loading.attr='disabled'>
							<i class="fa-solid fa-arrow-up"></i>
							Move Up
						</x-button>
					</div>
				@endif
				@if ($position < $maxStages)
					<div wire:loading.remove>
						<x-button class="btn-sm btn-light"
							wire:click="moveDown"
							wire:loading.attr='disabled'>
							<i class="fa-solid fa-arrow-down"></i>
							Move Down
						</x-button>
					</div>
				@endif
				<div wire:loading.remove>
					<x-button class="btn-sm btn-danger"
						wire:click="remove"
						wire:loading.attr='disabled'>
						<i class="fa-solid fa-trash"></i>
						<span>Remove</span>
					</x-button>
				</div>
				<div wire:loading.remove>
					<x-button class="btn-sm btn-success"
						wire:click="save"
						wire:loading.attr='disabled'>
						<i class="fa-solid fa-floppy-disk"></i>
						<span>Save</span>
					</x-button>
				</div>
			@endif
		</x-slot:footer>
	</form>
</x-card>
