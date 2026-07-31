@extends('layouts.app')
@section('content')
	<x-card>
		<x-slot:header>Client Information</x-slot:header>
		@include('client.partials.create.form', [
			'client' => $client,
			'readonly' => true,
		])
		@can('edit', $client)
			<x-slot:footer>
				<a name=""
					id=""
					class="btn btn-warning btn-sm"
					href="{{ route('client.edit', $client) }}"
					role="button">
					<i class="fa-solid fa-arrow-up-right-from-square"></i>
					Edit Data
				</a>
			</x-slot:footer>
		@endcan
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
		@isset($beneficiary)
			<a name=""
				id=""
				class="btn btn-sm btn-info"
				href="{{ route('beneficiary.show', $beneficiary) }}"
				role="button">
				<i class="fa-solid fa-arrow-up-right-from-square"></i>
				{{ $beneficiary->fullname() }} (Beneficiary)
			</a>
		@else
			<a name=""
				id=""
				class="btn btn-primary"
				href="{{ route('beneficiary.create') }}"
				role="button">
				<i class="fa-solid fa-arrow-up-right-from-square"></i>
				Create Beneficiary
			</a>
		@endisset
	</div>
@endsection
