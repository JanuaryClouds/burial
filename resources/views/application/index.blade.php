@extends('layouts.app')
@section('content')
	<x-card>
		@include('partials.datatable.index', [
			'columns' => $columns,
			'src' => 'data',
		])
		@unlessrole('staff')
			<x-slot:footer>
				<a href="{{ route('application.create') }}"
					class="btn btn-sm btn-light">
					<i class="fa fa-plus"></i>
					Create a New Application
				</a>
			</x-slot:footer>
		@endunlessrole
	</x-card>
@endsection
