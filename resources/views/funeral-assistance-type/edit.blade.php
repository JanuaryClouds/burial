@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-6">
		<x-card>
			<x-slot:header>Edit {{ $funeralAssistanceType->name }}</x-slot:header>
			<livewire:funeral-assistance-type.edit :funeralAssistanceType="$funeralAssistanceType"
				defer />
		</x-card>
	</div>
@endsection
