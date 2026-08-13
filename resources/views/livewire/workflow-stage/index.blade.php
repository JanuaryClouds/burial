<div class="d-flex flex-column gap-4">
	<h4 class="fs-2 fw-bold">{{ $stages->count() }} Stage(s)</h4>
	@foreach ($stages as $stage)
		<livewire:workflow-stage.show defer
			:stage="$stage"
			:key="'workflow-stage-' . $stage->uuid"
			:maxCount="$stages->count()" />
	@endforeach
	<div class="d-flex flex-center py-4">
		<button class="btn btn-primary"
			wire:click="addStage()">
			<i class="bi bi-plus-lg"></i>
			Add Stage
		</button>
	</div>
</div>
