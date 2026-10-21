<div class="d-flex flex-column gap-6"
	wire:poll.10s='loadData'>
	<x-card>
		@include('partials.dataTable.index', [
			'dataTableId' => $dataTableId,
			'columns' => $columns,
			'data' => $mappedLogs,
		])
	</x-card>
</div>
