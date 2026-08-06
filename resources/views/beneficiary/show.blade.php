@extends('layouts.app')
@section('content')
	<x-card>
		<x-slot:header>Beneficiary Information</x-slot:header>
		@include('beneficiary.partials.create.form', [
			'beneficiary' => $beneficiary,
			'readonly' => true,
		])
		@can('update', $beneficiary)
			<x-slot:footer>
				<a name=""
					id=""
					class="btn btn-sm btn-warning"
					href="{{ route('beneficiary.edit', $beneficiary) }}"
					role="button">
					<i class="fa-solid fa-arrow-up-right-from-square"></i>
					Edit Data
				</a>
			</x-slot:footer>
		@endcan
	</x-card>
	<x-card>
		<x-slot:header>Family Composition</x-slot:header>
		@foreach ($family as $member)
			@include('beneficiary.family.partials.show', [
				'family' => $member,
			])
			<hr>
		@endforeach
	</x-card>
	<div class="d-flex align-items-baseline gap-2">
		@isset($application)
			<a name=""
				id=""
				class="btn btn-sm btn-info"
				href="{{ route('application.show', $application) }}"
				role="button">
				<i class="fa-solid fa-arrow-up-right-from-square"></i>
				Application {{ $application->tracking_no }}
			</a>
		@endisset
		@isset($client)
			<a name=""
				id=""
				class="btn btn-sm btn-info"
				href="{{ route('client.show', $client) }}"
				role="button">
				<i class="fa-solid fa-arrow-up-right-from-square"></i>
				{{ $client->fullname() }} (Client)
			</a>
		@endisset
	</div>
@endsection
