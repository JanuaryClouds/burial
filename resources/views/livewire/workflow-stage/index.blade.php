<x-card>
	<x-slot:header>Stages</x-slot:header>
	@foreach ($stages as $stage)
		<livewire:workflow-stage.show :stage="$stage" />
	@endforeach
</x-card>
