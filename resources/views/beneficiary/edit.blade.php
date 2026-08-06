@extends('layouts.app')
@section('content')
	<form id="edit-beneficiary-form"
		action="{{ route('beneficiary.update', $beneficiary) }}"
		method="POST">
		@csrf
		@method('POST')
		<x-card>
			<x-slot:header>Edit Beneficiary Information</x-slot:header>
			@include('beneficiary.partials.create.form', [
				'beneficiary' => $beneficiary,
				'readonly' => !auth()->user()->can('update', $beneficiary),
			])
			<x-slot:footer>
				<a name=""
					id=""
					class="btn btn-sm btn-light"
					href="{{ app()->make('url')->previous() }}"
					role="button">
					<i class="fa-solid fa-xmark"></i>
					Back
				</a>
				@if (auth()->user()->can('edit', $beneficiary))
					<button class="btn btn-sm btn-primary"
						type="submit">
						<i class="fa-solid fa-floppy-disk"></i>
						Submit
					</button>
				@endif
			</x-slot:footer>
		</x-card>
	</form>
@endsection
