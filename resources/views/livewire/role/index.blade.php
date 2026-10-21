<div class="d-flex flex-column gap-6"
	wire:poll.10s='loadData'>
	<x-card>
		@include('partials.dataTable.index', [
			'columns' => $columns,
			'data' => $mappedRoles,
			'dataTableId' => $dataTableId,
		])
		<x-slot:footer>
			<a href="{{ route('role.create') }}"
				class="btn btn-primary">
				<x-icon.font-awesome class="fa-plus-circle" />
				Create New Role
			</a>
		</x-slot:footer>
	</x-card>
</div>
