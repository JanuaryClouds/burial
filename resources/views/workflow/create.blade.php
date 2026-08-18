@extends('layouts.app')
@section('content')
	<x-card>
		<x-slot:header>Create New Workflow</x-slot:header>
		<form action="{{ route('workflow.store') }}"
			id="workflowForm"
			method="post">
			@csrf
			<x-form.input name="name"
				label="Name of the New Workflow"
				class="required"
				required />
			<x-form.textarea name="description"
				class="required"
				label="Description of the New Workflow"
				required />
		</form>
		<x-slot:footer>
			<a class="btn btn-secondary"
				href="{{ route('workflow.index') }}"
				role="button">
				<i class="fa-solid fa-xmark"></i>
				Cancel
			</a>
			<x-button type="submit"
				class="btn btn-success"
				form="workflowForm"
				wire:loading.attr="disabled">
				<i class="fa-solid fa-floppy-disk"></i>
				<span wire:loading.remove>Save Workflow</span>
				<span wire:loading>Saving...</span>
			</x-button>
		</x-slot:footer>
	</x-card>
@endsection
