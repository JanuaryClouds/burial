@extends('layouts.app')
@section('content')
	<x-card>
		<x-slot:header>{{ $client->fullname() }}</x-slot:header>
		<livewire:client.show :client="$client" />
		<x-slot:footer>
			<a href="{{ route('client.index') }}"
				class="btn btn-sm btn-light">
				<x-icon.font-awesome class="fa-arrow-left" />
				Back
			</a>
			@can('update', [\App\Models\Client::class, $client])
				<a href="{{ route('client.edit', $client) }}"
					class="btn btn-sm btn-light">
					<x-icon.font-awesome class="fa-pencil" />
					Edit
				</a>
			@endcan
            @php($recordApplication = $client->application ?? $client->claimantAssignments->first()?->application)
			@if ($recordApplication)
				<a href="{{ route('application.show', $recordApplication) }}"
					class="btn btn-sm btn-info">
					<x-icon.font-awesome class="fa-external-link" />
					Application {{ $recordApplication->tracking_no }}
				</a>
			@endif
		</x-slot:footer>
	</x-card>
@endsection
