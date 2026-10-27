<div class="d-flex flex-column gap-6"
	wire:poll.10s="loadData">
	{{-- start::Statistics --}}
	@can('office-access')
		<div>
			@include('client.index.partials.statistics')
		</div>
	@endcan
	{{-- end::Statistics --}}

	{{-- start::Index --}}
	<div>
		<x-card>
			@include('partials.dataTable.index', [
				'columns' => $columns,
				'data' => $mappedClients,
				'dataTableId' => $dataTableId,
			])
			@if (! auth()->user()->isInternal())
				<x-slot:footer>
					<a href="{{ route('client.create') }}"
						class="btn btn-sm btn-primary">
						<x-icon.font-awesome class="fa-plus" />
						New Client
					</a>
				</x-slot:footer>
			@endif
		</x-card>
	</div>
	{{-- end::Index --}}

	{{-- start::charts --}}
	@can('office-access')
		<div>
			@include('client.index.partials.charts')
		</div>
	@endcan
	{{-- end::charts --}}
</div>
