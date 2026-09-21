@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-4">
		<x-card>
			<livewire:beneficiary.family.edit :member="$member" />
		</x-card>
		<div class="d-flex justify-content-start gap-2">
			<a href="{{ route('beneficiary.show', $member->beneficiary) }}"
				class="btn btn-sm btn-info">
				<x-icon.font-awesome class="fa-external-link" />
				{{ $member->beneficiary->fullname() }}
			</a>
			@if ($member->beneficiary->application)
				<a href="{{ route('application.show', $member->beneficiary->application) }}"
					class="btn btn-sm btn-info">
					<x-icon.font-awesome class="fa-external-link" />
					{{ $member->beneficiary->application->tracking_no }}
				</a>
			@endif
		</div>
	</div>
@endsection
