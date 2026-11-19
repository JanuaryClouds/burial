@extends('layouts.app')
@section('content')
    @if(auth()->id() === $user->id && auth()->user()->hasRole('superadmin'))
        <a class="btn btn-light mb-4" href="{{ route('mfa.manage') }}">Authenticator security</a>
    @endif
	<div class="d-flex flex-column gap-6">
		<x-card>
			<livewire:user.show :user="$user"
				defer />
		</x-card>
	</div>
@endsection
