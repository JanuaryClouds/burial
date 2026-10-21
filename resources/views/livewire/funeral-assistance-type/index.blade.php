<div class="d-flex flex-column gap-6"
	wire:poll.10s='loadData'>
	<x-card>
		@include('partials.dataTable.index', [
			'columns' => $columns,
			'data' => $mappedFuneralAssistanceTypes,
			'dataTableId' => $dataTableId,
		])
	</x-card>
	<x-card>
		<x-slot:header>Create New Type of Funeral Assistance Type</x-slot:header>
		<livewire:funeral-assistance-type.create defer />
	</x-card>
</div>
