@extends('layouts.app')
@section('content')
	<x-card>
		@include('partials.datatable.index', [
			'columns' => $columns,
			'src' => 'data',
		])
		@role('superadmin')
			<x-slot:footer>
				<a href="{{ route('workflow.create') }}"
					class="btn btn-sm btn-light">
					<i class="fa fa-plus"></i>
					Create New Workflow
				</a>
			</x-slot:footer>
		@endrole
	</x-card>
@endsection
