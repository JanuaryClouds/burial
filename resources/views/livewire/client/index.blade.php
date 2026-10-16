<div class="d-flex flex-column gap-6">
	{{-- start::Statistics --}}
	<div wire:poll.10s>
		@include('client.index.partials.statistics')
	</div>
	{{-- end::Statistics --}}

	{{-- start::Index --}}
	<div wire:ignore>
		<x-card>
			@include('partials.datatable.index', [
				'columns' => $columns,
				'src' => 'data',
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
	<div wire:poll.10s>
		@include('client.index.partials.charts')
	</div>
	{{-- end::charts --}}
</div>
