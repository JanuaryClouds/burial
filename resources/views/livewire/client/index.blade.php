<div class="d-flex flex-column gap-6"
	wire:poll.10s="loadData">
	{{-- start::Statistics --}}
	@role('staff')
		<div>
			@include('client.index.partials.statistics')
		</div>
	@endrole
	{{-- end::Statistics --}}

	{{-- start::Index --}}
	<div>
		<x-card>
			@include('partials.dataTable.index', [
				'columns' => $columns,
				'data' => $mappedClients,
				'dataTableId' => $dataTableId,
			])
			@unlessrole('staff')
				<x-slot:footer>
					<a href="{{ route('client.create') }}"
						class="btn btn-sm btn-primary">
						<x-icon.font-awesome class="fa-plus" />
						New Client
					</a>
				</x-slot:footer>
			@endunlessrole
		</x-card>
	</div>
	{{-- end::Index --}}

	{{-- start::charts --}}
	@role('staff')
		<div>
			@include('client.index.partials.charts')
		</div>
	@endrole
	{{-- end::charts --}}
</div>
