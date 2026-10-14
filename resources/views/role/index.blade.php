@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-6">
		<x-card>
			@include('partials.datatable.index', [
				'columns' => $columns,
				'src' => 'data',
			])
			<x-slot:footer>
				<a href="{{ route('role.create') }}"
					class="btn btn-primary">
					<x-icon.font-awesome class="fa-plus-circle" />
					Create New Role
				</a>
			</x-slot:footer>
		</x-card>
	</div>
@endsection
