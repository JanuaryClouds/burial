@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-6">
		<x-card>
			<x-slot:header>Edit {{ $funeralAssistanceType->name }}</x-slot:header>
			<livewire:funeral-assistance-type.edit :funeralAssistanceType="$funeralAssistanceType"
				defer />
		</x-card>

        <x-card>
            <x-slot:header>Accessory Document Templates</x-slot:header>
            <livewire:funeral-assistance-type.templates :funeralAssistanceType="$funeralAssistanceType" />
        </x-card>
	</div>
@endsection
