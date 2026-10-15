@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-6">
		<x-card>
			<x-slot:header>Edit {{ $user->fullname() }}</x-slot:header>
			<livewire:user.edit :user="$user"
				defer />
		</x-card>
	</div>
@endsection
