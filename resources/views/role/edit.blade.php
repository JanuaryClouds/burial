@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-6">
		<x-card>
			<x-slot:header>{{ $pageTitle }}</x-slot:header>
			<livewire:role.edit :role="$role"
				defer />
		</x-card>
	</div>
@endsection
