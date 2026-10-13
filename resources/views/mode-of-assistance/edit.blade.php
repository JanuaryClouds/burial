@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-6">
		<x-card>
			<x-slot:header>Edit {{ $modeOfAssistance->name }}</x-slot:header>
			<livewire:mode-of-assistance.edit :modeOfAssistance="$modeOfAssistance"
				defer />
		</x-card>
	</div>
@endsection
