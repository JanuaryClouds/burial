<div class='d-flex flex-column gap-6'
	wire:poll.10s='loadData'>
	@can('office-access')
		{{-- start::Statistics --}}
		<div>
			@include('beneficiary.index.partials.statistics')
		</div>
		{{-- end::Statistics --}}
	@endcan

	{{-- start::Index --}}
	<div>
		<x-card>
			@include('partials.dataTable.index', [
				'columns' => $columns,
				'dataTableId' => $dataTableId,
			])
			@if (! auth()->user()->isInternal())
				<x-slot:footer>
					<a href="{{ route('beneficiary.create') }}"
						class="btn btn-sm btn-primary">
						<x-icon.font-awesome class="fa-plus" />
						New Beneficiary
					</a>
				</x-slot:footer>
			@endif
		</x-card>
	</div>
	{{-- end::Index --}}

	@can('office-access')
		{{-- start::Charts --}}
		<div>
			@include('beneficiary.index.partials.charts')
		</div>
		{{-- end::Charts --}}
	@endcan
</div>
