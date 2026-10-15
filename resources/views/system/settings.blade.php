@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-6">
		<x-card>
			<x-slot:header>System Settings</x-slot:header>
			<livewire:system.settings.edit defer />
		</x-card>
	</div>
@endsection
