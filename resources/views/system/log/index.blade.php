@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-6">
		<x-card>
			@include('partials.datatable.index', [
				'columns' => $columns,
				'src' => 'data',
			])
		</x-card>
	</div>
@endsection
