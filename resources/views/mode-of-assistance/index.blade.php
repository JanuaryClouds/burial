@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-6">
		<x-card>
			@include('partials.dataTable.index', [
				'columns' => $columns,
				'src' => 'data',
			])
		</x-card>
		<x-card>
			<x-slot:header>Create New Mode of Assistance</x-slot:header>
			<livewire:mode-of-assistance.create defer />
		</x-card>
	</div>
@endsection
