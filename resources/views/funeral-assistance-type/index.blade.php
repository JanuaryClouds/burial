@extends('layout.app')
@section('content')
	<div class="d-flex flex-column gap-6">
		<x-card>
			@include('partials.dataTable.index', [
				'columns' => $columns,
				'src' => 'data',
			])
		</x-card>
		<x-card>
			<x-slot:header>Create New Type of Funeral Assistance Type</x-slot:header>
			<livewire:funeral-assistance-type.create defer />
		</x-card>
	</div>
@endsection
