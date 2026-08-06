@extends('layouts.app')
@section('content')
	<x-card>
		<x-slot:header>Beneficiary's Family Member Information</x-slot:header>
		@include('beneficiary.family.partials.show', [
			'member' => $member,
		])
	</x-card>
	<div class="d-flex align-items-baseline gap-2">
		<a name=""
			id=""
			class="btn btn-info btn-sm"
			href="{{ route('application.show', $application) }}"
			role="button">
			<i class="fa-solid fa-arrow-up-right-from-square"></i>
			Application {{ $application->tracking_no }}
		</a>
		<a name=""
			id=""
			class="btn btn-info btn-sm"
			href="{{ route('beneficiary.show', $beneficiary) }}"
			role="button">
			<i class="fa-solid fa-arrow-up-right-from-square"></i>
			{{ $beneficiary->fullname() }} (Beneficiary)
		</a>
	</div>
@endsection
