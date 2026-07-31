@extends('layouts.app')
@section('content')
	<x-card>
		@include('partials.datatable.index', [
			'columns' => $columns,
			'src' => 'data',
		])
	</x-card>
@endsection
