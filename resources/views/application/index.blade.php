@extends('layouts.app')
@section('content')
	{{-- start::Statistics --}}
	@include('application.index.partials.statistics')
	{{-- end::Statistics --}}

	{{-- start::Index --}}
	<x-card>
		@include('partials.datatable.index', [
			'columns' => $columns,
			'src' => 'data',
		])
		<x-slot:footer>
			@role('staff')
				<a href="{{ route('application.search') }}"
					class="btn btn-sm btn-primary"
					role="button">
					<x-icon.font-awesome class="fa-qrcode" />
					Scan Barcode
				</a>
			@endrole
			@unlessrole('staff')
				<a href="{{ route('application.create') }}"
					class="btn btn-sm btn-primary">
					<x-icon.font-awesome class="fa-plus" />
					New Application
				</a>
			@endunlessrole
		</x-slot:footer>
	</x-card>
	{{-- end::Index --}}

	@role('staff')
		{{-- start::Charts --}}
		@include('application.index.partials.charts')
		{{-- end::Charts --}}
	@endrole
@endsection
