<div class="d-flex flex-column gap-6"
	wire:poll.10s="loadData">
	{{-- start::Statistics --}}
	<div>
		@include('application.index.partials.statistics')
	</div>
	{{-- end::Statistics --}}

	{{-- start::Index --}}
	<div>
		<x-card>
			@include('partials.dataTable.index', [
				'columns' => $columns,
				'data' => $mappedApplications,
				'dataTableId' => $dataTableId,
			])
			<x-slot:footer>
				@role('staff')
					<a href="{{ route('report.index') }}"
						class="btn btn-primary">
						<x-icon.font-awesome class="fa-chart-bar" />
						Generate Report
					</a>
					<a href="{{ route('application.search') }}"
						class="btn btn-primary">
						<x-icon.font-awesome class="fa-qrcode" />
						Scan Barcode
					</a>
				@endrole
				@unlessrole('staff')
					<a href="{{ route('application.create') }}"
						class="btn btn-primary">
						<x-icon.font-awesome class="fa-plus" />
						New Application
					</a>
				@endunlessrole
			</x-slot:footer>
		</x-card>
	</div>
	{{-- end::Index --}}

	@role('staff')
		{{-- start::Charts --}}
		<div>
			{{-- @include('application.index.partials.charts') --}}
		</div>
		{{-- end::Charts --}}
	@endrole
</div>
