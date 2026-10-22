<div wire:poll.10s='loadData'
	class="d-flex flex-column gap-6">
	<x-card>
		@include('partials.dataTable.index', [
			'dataTableId' => $dataTableId,
			'columns' => $columns,
			'data' => $mappedDocuments,
		])
	</x-card>
	<x-card>
		<x-slot:header>
			Create new Document to be submitted
		</x-slot:header>
		<livewire:document-requirement.create />
	</x-card>
</div>
