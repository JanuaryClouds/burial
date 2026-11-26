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
			<x-data-table :navigate="true" :columns="$columns" :data="$mappedApplications" :data-table-id="$dataTableId"
                :card-fields="['primary' => 'tracking_no', 'secondary' => 'client', 'tertiary' => 'beneficiary', 'badge' => 'status', 'date' => 'application_date']" />
			<x-slot:footer>
				@can('office-access')
                    @can('view-reports')
					<a href="{{ route('report.index') }}"
						class="btn btn-primary">
						<x-icon.font-awesome class="fa-chart-bar" />
						Generate Report
					</a>
                    @endcan
					<a wire:navigate href="{{ route('application.search') }}"
						class="btn btn-primary">
						<x-icon.font-awesome class="fa-qrcode" />
						Scan Barcode
					</a>
				@endcan

                @can('walk-in-intake')
                    <a href="{{ route('walk-in.index') }}" class="btn btn-primary">
                        <x-icon.font-awesome class="fa-person-walking" />
                        Walk-in Intake
                    </a>
                @endcan
                @include('components.record-export-trigger')
				@if (! auth()->user()->isInternal())
					<a wire:navigate href="{{ route('application.create') }}"
						class="btn btn-primary">
						<x-icon.font-awesome class="fa-plus" />
						New Application
					</a>
				@endif
			</x-slot:footer>
		</x-card>
	</div>
	{{-- end::Index --}}

	@can('office-access')
		{{-- start::Charts --}}
		<div>
			@include('application.index.partials.charts')
		</div>
		{{-- end::Charts --}}
	@endcan
</div>
