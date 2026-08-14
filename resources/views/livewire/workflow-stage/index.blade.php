<div class="d-flex flex-column gap-4">
	<h4 class="fs-2 fw-bold">{{ $stages->count() }} Stage(s)</h4>
	<div class="row gy-4">
		@foreach ($stages as $stage)
			<div class="col-12 col-lg-6">
				<livewire:workflow-stage.show defer
					:stage="$stage"
					:key="'workflow-stage-' . $stage->uuid"
					:maxCount="$stages->count()" />
			</div>
		@endforeach
	</div>
	<div class="d-flex flex-center py-4">
		<button class="btn btn-primary"
			wire:click="addStage()">
			<i class="fa-solid fa-plus"></i>
			Add Stage
		</button>
	</div>
	<h4 class="fs-2 fw-bold">Retired Stages: {{ $trashedStages->count() }}</h4>
	<div class="row gy-4">
		@foreach ($trashedStages as $stage)
			<div class="col-12 col-lg-6">
				<livewire:workflow-stage.show defer
					:stage="$stage"
					:key="'workflow-stage-' . $stage->uuid"
					:maxCount="$trashedStages->count()" />
			</div>
		@endforeach
	</div>
</div>
