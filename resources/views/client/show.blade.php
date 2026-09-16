@extends('layouts.app')
@section('content')
	<x-card>
		<x-slot:header>{{ $client->fullname() }}</x-slot:header>
		<livewire:client.show :client="$client" />
		<x-slot:footer>
			<a href="{{ route('client.index') }}"
				class="btn btn-sm btn-light">
				<i class="fa-solid fa-arrow-left"></i>
				Back
			</a>
			@can('update', [\App\Models\Client::class, $client])
				<a href="{{ route('client.edit', $client) }}"
					class="btn btn-sm btn-light">
					<i class="fa-solid fa-pencil"></i>
					Edit
				</a>
			@endcan
		</x-slot:footer>
	</x-card>
@endsection
