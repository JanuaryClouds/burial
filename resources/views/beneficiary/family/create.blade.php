@extends('layouts.app')
@section('content')
	<x-card>
		<form action="{{ route('family.store') }}"
			method="post">
			@csrf
			<x-slot:header>Register Family Members</x-slot:header>
			@include('livewire.beneficiary.family-composition')
			<x-slot:footer>
				<a name=""
					id=""
					class="btn btn-primary"
					href="#"
					role="button">
					<i class="fa-solid fa-xmark"></i>
					Cancel
				</a>
				<x-modal>
					<p class="fs-4">
						Are you sure you want to save these family members to the
					</p>
				</x-modal>
			</x-slot:footer>
		</form>
	</x-card>
@endsection
