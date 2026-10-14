@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-6">
		<x-card>
			<livewire:role.create defer />
		</x-card>
	</div>
@endsection
