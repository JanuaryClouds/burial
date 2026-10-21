<div class='d-flex flex-column gap-6'
	wire:poll.10s='loadData'>
	@role('staff')
		{{-- start::Statistics --}}
		<div>
			@include('beneficiary.index.partials.statistics')
		</div>
		{{-- end::Statistics --}}
	@endrole

	{{-- start::Index --}}
	<div>
		<x-card>
			@include('partials.dataTable.index', [
				'columns' => $columns,
				'dataTableId' => $dataTableId,
			])
			@unlessrole('staff')
				<x-slot:footer>
					<a href="{{ route('beneficiary.create') }}"
						class="btn btn-sm btn-primary">
						<x-icon.font-awesome class="fa-plus" />
						New Beneficiary
					</a>
				</x-slot:footer>
			@endunlessrole
		</x-card>
	</div>
	{{-- end::Index --}}

	@role('staff')
		{{-- start::Charts --}}
		<div>
			@include('beneficiary.index.partials.charts')
		</div>
		{{-- end::Charts --}}
	@endrole
</div>
