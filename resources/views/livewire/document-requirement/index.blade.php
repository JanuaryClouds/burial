<div wire:poll.10s='loadData'
	class="d-flex flex-column gap-6">
	<x-card>
		<x-data-table :columns="$columns" :data="$mappedDocuments" :data-table-id="$dataTableId"
            :card-fields="['primary' => 'name', 'secondary' => 'source', 'date' => 'updated_at']" />
	</x-card>
	<x-card>
		<x-slot:header>
			Create new Document to be submitted
		</x-slot:header>
		<livewire:document-requirement.create />
	</x-card>
</div>
