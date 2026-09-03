@extends('layouts.app')
@section('content')
	{{-- Partial: Client Details --}}
	<x-card>
		@include('client.partials.show')
		@can('update', $client)
			<x-slot:footer>
				<a name=""
					id=""
					class="btn btn-warning btn-sm"
					href="{{ route('client.edit', $client) }}"
					role="button">
					<x-icon.font-awesome :icon="'arrow-up-right-from-square'" />
					Edit Data
				</a>
			</x-slot:footer>
		@endcan
	</x-card>

	{{-- Partial: Related Links --}}
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

	{{-- Partial: Interview Schedule and Form --}}
	<div class="row">
		<div class="col-12 col-lg-4">
			<x-card>
				<x-slot:header>Interview Schedule</x-slot:header>
				<livewire:interview.index :client="$client" />
			</x-card>
		</div>
		<div class="col-12 col-lg-8">
			<livewire:interview.create :client="$client" />
		</div>
	</div>
@endsection
