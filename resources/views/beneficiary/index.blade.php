@extends('layouts.app')
@section('content')
	@role('staff')
		{{-- start::Statistics --}}
		@include('beneficiary.index.partials.statistics')
		{{-- end::Statistics --}}
	@endrole

	{{-- start::Index --}}
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
	{{-- end::Index --}}

	@role('staff')
		{{-- start::Charts --}}
		@include('beneficiary.index.partials.charts')
		{{-- end::Charts --}}
	@endrole
@endsection
