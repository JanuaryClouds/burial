<div class="d-flex flex-column gap-6"
	wire:poll.10s='loadData'>
	<x-card>
		@include('partials.dataTable.index', [
			'columns' => $columns,
			'dataTableId' => $dataTableId,
		])
		@role('superadmin')
			<x-slot:footer>
				<a href="{{ route('workflows.create') }}"
					class="btn btn-primary">
					<x-icon.font-awesome class="fa-plus" />
					Create Workflow
				</a>
			</x-slot:footer>
		@endrole
	</x-card>
	<x-card>
		<x-slot:header>Create New Workflow</x-slot:header>
		<livewire:workflow.create defer />
	</x-card>
</div>
