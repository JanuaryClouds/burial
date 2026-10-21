<div class="d-flex flex-column gap-6"
	wire:poll.10s='loadData'>
	<x-card>
		@include('partials.dataTable.index', [
			'columns' => $columns,
			'data' => $mappedModeOfAssistances,
			'dataTableId' => $dataTableId,
		])
	</x-card>
	<x-card>
		<x-slot:header>Create New Mode of Assistance</x-slot:header>
		<livewire:mode-of-assistance.create defer />
	</x-card>
</div>
