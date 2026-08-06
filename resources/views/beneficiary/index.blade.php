@extends('layouts.app')
@section('content')
	<x-card>
		@include('partials.datatable.index', [
			'src' => 'data',
			'columns' => $columns,
		])
		@unlessrole('staff')
			<x-slot:footer>
				<a href="{{ route('beneficiary.create') }}"
					class="btn btn-sm btn-light">
					<i class="fa fa-plus"></i>
					Register a New Beneficiary
				</a>
			</x-slot:footer>
		@endunlessrole
	</x-card>
@endsection
