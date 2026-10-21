<div class="d-flex flex-column gap-6"
	wire:poll.10s='loadData'>
	<x-card>
		@include('partials.dataTable.index', [
			'columns' => $columns,
			'data' => $mappedUsers,
			'dataTableId' => $dataTableId,
		])
	</x-card>
	<x-card>
		<x-slot:header>Create New Staff</x-slot:header>
		<livewire:user.create defer />
	</x-card>
</div>
