<div class='d-flex flex-column gap-6'>
	@role('staff')
		{{-- start::Statistics --}}
		<div wire:poll.10s>
			@include('beneficiary.index.partials.statistics')
		</div>
		{{-- end::Statistics --}}
	@endrole

	{{-- start::Index --}}
	<div wire:poll.30s>
		<x-card>
			@include('partials.datatable.index', [
				'src' => 'data',
				'columns' => $columns,
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
		<div wire:poll.10s>
			@include('beneficiary.index.partials.charts')
		</div>
		{{-- end::Charts --}}
	@endrole
</div>
