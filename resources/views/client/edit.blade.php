@extends('layouts.app')
@section('content')
	<form id="edit-client-form"
		action="{{ route('client.update', $client) }}"
		method="POST">
		@csrf
		@method('POST')
		<x-card>
			<x-slot:header>Edit Client Information</x-slot:header>
			@include('client.partials.create.form', [
				'client' => $client,
				'readonly' => !auth()->user()->can('update', $client),
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
				@if (auth()->user()->can('edit', $client))
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
