@extends('layouts.app')
@section('content')
	<x-card>
		@include('partials.datatable.index', [
			'src' => 'data',
			'columns' => $columns,
		])
	</x-card>
@endsection
