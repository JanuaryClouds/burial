<div class="d-flex flex-column gap-4">
	<h4 class="fs-2 fw-bold">{{ $stages->count() }} Stage(s)</h4>
	<div class="row gy-4">
		@for ($i = 1; $i <= $stages->count(); $i++)
			<div class="col-12 col-lg-6">
				<livewire:workflow-stage.show defer
					:position="$i"
					:stage="$stages->firstWhere('position', $i)"
					:workflowPermissions="$workflowPermissions"
					:key="'stage-position-' . $i"
					:maxStages="$stages->count()" />
			</div>
		@endfor
	</div>
	<div class="d-flex flex-center my-4">
		<x-button wire:click='addStage'
			wire:loading.attr='disabled'
			class="btn-primary fw-bold">
			<i class="fa-solid fa-plus"></i>
			Add Stage
		</x-button>
	</div>
	<h4 class="fs-2 fw-bold">{{ $trashedStages->count() }} Retired Stage(s)</h4>
	<div class="row gy-4">
		@foreach ($trashedStages as $trashedStage)
			<div class="col-12 col-lg-3">
				<x-card>
					<x-slot:header>
						{{ $trashedStage->name }}
						<x-slot:toolbar>
							<div wire:loading>
								<div class="spinner-border text-success"
									role="status">
									<span class="visually-hidden">Loading...</span>
								</div>
							</div>
						</x-slot:toolbar>
					</x-slot:header>
					<div wire:loading.remove>
						<p class="text-muted">{{ $trashedStage->description }}</p>
					</div>
					<div wire:loading>
						<p class="text-muted">Restoring stage...</p>
					</div>
					<x-slot:footer>
						<x-button wire:click="$dispatch('restore-stage', {uuid: '{{ $trashedStage->uuid }}'})"
							class="btn-primary btn-sm fw-bold"
							wire:loading.attr='disabled'>
							<i class="fa-solid fa-trash-arrow-up"></i>
							Restore Stage
						</x-button>
					</x-slot:footer>
				</x-card>
			</div>
		@endforeach
	</div>
</div>
