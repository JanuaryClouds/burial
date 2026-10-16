<div class="d-flex flex-column gap-6">
	<x-card>
		<div class="d-flex justify-content-between align-items-center"
			wire:poll.10s>
			<h4>Applications</h4>
			<a href="{{ route('application.index') }}"
				class="fs-4 btn btn-primary">
				Total Applications : {{ $applications->count() }}
			</a>
		</div>
	</x-card>

	{{-- start::Statistics --}}
	<div wire:poll.10s>
		@include('dashboard.partials.statistics')
	</div>
	{{-- end::Statistics --}}

	@unlessrole('staff')
		<div wire:ignore>
			<x-card>
				@include('partials.datatable.index', [
					'columns' => $columns,
					'src' => 'data',
				])
			</x-card>
		</div>
	@endunlessrole

	@role('staff')
		<div wire:poll.10s>
			{{-- start::Charts --}}
			@include('dashboard.partials.charts')
			{{-- end::Charts --}}
		</div>
	@endrole
</div>
