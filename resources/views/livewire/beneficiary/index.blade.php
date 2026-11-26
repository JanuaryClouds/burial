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
			<x-data-table :navigate="true" :columns="$columns" :data="$mappedBeneficiaries" :data-table-id="$dataTableId"
                :card-fields="['primary' => 'name', 'secondary' => 'age', 'tertiary' => 'place_of_birth', 'badge' => 'application', 'date' => 'recorded']" />
			@if (! auth()->user()->isInternal() || auth()->user()->can('create-reports'))
				<x-slot:footer>
					@include('components.record-export-trigger')
					@if (! auth()->user()->isInternal())
						<a wire:navigate href="{{ route('beneficiary.create') }}"
							class="btn btn-sm btn-primary">
							<x-icon.font-awesome class="fa-plus" />
							New Beneficiary
						</a>
					@endif
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
