<div>
	{{-- start::Menu Bar --}}
	<div class="d-flex justify-content-between align-items-center">
		{{-- start::Data Count --}}
		<div>
			<p class="text-muted small">Showing {{ $perPage }} out of {{ $total }}</p>
		</div>
		{{-- end::Data Count --}}

		{{-- start::Search --}}
		<div class="d-flex align-items-center gap-1">
			<x-form.input wire:model.live='search'
				name="search"
				placeholder="Search" />
		</div>
		{{-- end::Search --}}
	</div>
	{{-- end::Menu Bar --}}

	{{-- start::Header Row --}}
	<div class="row fw-bold py-1 bg-secondary-subtle">
		{{ $headerRow }}
	</div>
	{{-- end::Header Row --}}

	{{-- start::Table Rows --}}
	<div class="row">
		{{ $tableRows }}
	</div>
	{{-- end::Table Rows --}}

	{{-- start::Pagination Menu --}}
	<div class="d-flex justify-content-between align-items-center gap-2">
		{{-- start::Count Per Page --}}
		<div>
			<p class="text-muted small">Showing {{ $perPage }} out of {{ $total }}</p>
		</div>
		{{-- end::Count Per Page --}}
		{{-- start::Paginations --}}
		<div>
			{{ $paginations }}
		</div>
		{{-- end::Paginations --}}
	</div>
	{{-- end::Pagination Menu --}}
</div>
