@extends('layouts.app')
@section('content')
	<x-card>
		<x-slot:header>{{ $workflow->name }}</x-slot:header>
		<p class="fs-4">{{ $workflow->description }}</p>
		<x-slot:footer>
			<a href="{{ route('workflow.index') }}"
				class="btn btn-sm btn-secondary">
				<i class="fa-solid fa-up-right-from-square"></i>
				Back
			</a>
			@can('update', $workflow)
				<a href="{{ route('workflow.edit', $workflow) }}"
					class="btn btn-sm btn-warning">
					<i class="fa-solid fa-up-right-from-square"></i>
					Edit
				</a>
			@endcan
		</x-slot:footer>
	</x-card>
	<livewire:workflow-stage.index :workflow="$workflow" />
@endsection
