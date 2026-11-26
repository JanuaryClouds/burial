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
			<x-data-table :navigate="true" :columns="$columns" :data="$mappedClients" :data-table-id="$dataTableId"
                :card-fields="['primary' => 'name', 'secondary' => 'address', 'tertiary' => 'application_tracking_no', 'date' => 'recorded_at']" />
			@if (! auth()->user()->isInternal() || auth()->user()->can('create-reports'))
				<x-slot:footer>
					@include('components.record-export-trigger')
					@if (! auth()->user()->isInternal())
						<a wire:navigate href="{{ route('client.create') }}"
							class="btn btn-sm btn-primary">
							<x-icon.font-awesome class="fa-plus" />
							New Client
						</a>
					@endif
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
