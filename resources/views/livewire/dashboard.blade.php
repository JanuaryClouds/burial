<div class="d-flex flex-column gap-6"
	wire:poll.10s="loadData">
	<x-card>
		<div class="d-flex justify-content-between align-items-center">
			<h4>Applications</h4>
			<a href="{{ route('application.index') }}"
				class="fs-4 btn btn-primary">
				Total Applications : {{ $applications->count() }}
			</a>
		</div>
	</x-card>

	{{-- start::Statistics --}}
	<div>
		@include('dashboard.partials.statistics')
	</div>
	{{-- end::Statistics --}}

	@unlessrole('staff')
		{{-- start::Datatable --}}
		<div>
			<x-card>
				@include('partials.dataTable.index', [
					'columns' => $columns,
					'data' => $mappedApplications,
					'dataTableId' => $dataTableId,
				])
			</x-card>
		</div>
		{{-- end::Datatable --}}
	@endunlessrole

	@role('staff')
		{{-- start::Charts --}}
		<div>
			@include('dashboard.partials.charts')
		</div>
		{{-- end::Charts --}}
	@endrole
</div>
