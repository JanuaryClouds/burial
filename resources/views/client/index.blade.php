@extends('layouts.app')
@section('content')
	<x-card>
		@include('partials.datatable.index', [
			'columns' => $columns,
			'src' => 'data',
		])
		@unlessrole('staff')
			<x-slot:footer>
				<a href="{{ route('client.create') }}"
					class="btn btn-sm btn-light">
					<i class="fa fa-plus"></i>
					Register as a New Client
				</a>
			</x-slot:footer>
		@endunlessrole
	</x-card>
@endsection
