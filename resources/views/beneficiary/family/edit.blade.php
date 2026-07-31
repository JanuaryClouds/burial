@extends('layouts.app')
@section('content')
	<form id="edit-beneficiary-family-form"
		action="{{ route('family.update', $member) }}"
		method="POST">
		@csrf
		@method('POST')
		<x-card>
			<x-slot:header>Edit Beneficiary Family Information</x-slot:header>
			@include('beneficiary.family.partials.show', [
				'member' => $member,
				'readonly' => !auth()->user()->hasRole('superadmin'),
			])
			<x-slot:footer>
				<a name=""
					id=""
					class="btn btn-sm btn-light"
					href="{{ route('family.show', $member) }}"
					role="button">
					<i class="fa-solid fa-xmark"></i>
					Back
				</a>
				@if (auth()->user()->hasRole('superadmin'))
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
