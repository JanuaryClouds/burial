@extends('layouts.app')
@section('content')
	{{-- start::Statistics --}}
	@include('client.index.partials.statistics')
	{{-- end::Statistics --}}

	{{-- start::Index --}}
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
	{{-- end::Index --}}

	{{-- start::charts --}}
	@include('client.index.partials.charts')
	{{-- end::charts --}}
@endsection
