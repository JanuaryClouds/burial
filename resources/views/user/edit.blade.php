@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-6">
		<x-card>
			<x-slot:header>Edit {{ $user->fullname() }}</x-slot:header>
			<livewire:user.edit :user="$user"
				defer />
		</x-card>
		@can('changePassword', $user)
			<x-card>
				<x-slot:header>Change Your Password</x-slot:header>
				<livewire:user.change-password />
			</x-card>
		@endcan
	</div>
@can('changePassword', $user)
    <x-card><x-slot:header>Change Your Password</x-slot:header><livewire:user.change-password /></x-card>
@endcan
@endsection
