<x-card>
	<x-slot:header>
		@if ($client)
			<i class="fa-solid fa-check-circle me-2 fs-4 text-success"></i>
			Selected Client: {{ $client->fullname() }}
		@else
			<i class="fa-solid fa-question-circle me-2 fs-4 text-warning"></i>
			No selected Client
		@endif
	</x-slot:header>
	@if ($client)
		@include('client.partials.create.form', [
			'client' => $client,
			'readonly' => true,
		])
	@endif
	<x-slot:footer>
		<a name=""
			id=""
			class="btn btn-light btn-sm"
			href="{{ route('client.create') }}"
			role="button">
			<i class="fa-solid fa-plus"></i>
			Create Another Client Record
		</a>
		@if ($client && auth()->user()->can('update', $client))
			<a name=""
				id=""
				class="btn btn-warning btn-sm"
				href="{{ route('client.edit', $client) }}"
				role="button">
				<i class="fa-solid fa-arrow-up-right-from-square"></i>
				Edit
			</a>
		@endif
	</x-slot:footer>
</x-card>
