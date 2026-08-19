@extends('layouts.app')
@section('content')
	@role('staff')
		@include('application.partials.menu')
	@endrole
	<div class="row">
		<div class="col-12 col-lg-8">
			@include('application.partials.timeline', [
				'application' => $application,
			])
		</div>
		<div class="col-6 col-lg-4">
			@include('application.partials.codes', [
				'application' => $application,
				'qrCode' => $qrCode,
				'barcode' => $barcode,
			])
		</div>
	</div>
	<x-card>
		<x-slot:header>Client</x-slot:header>
		@include('client.partials.create.form', [
			'client' => $application->client,
			'readonly' => true,
		])
		<x-slot:footer>
			<a href="{{ route('client.show', $client) }}"
				class="btn btn-sm btn-info">
				<i class="fa-solid fa-arrow-up-right-from-square"></i>
				View Client
			</a>
		</x-slot:footer>
	</x-card>
	<x-card>
		<x-slot:header>Beneficiary</x-slot:header>
		@include('beneficiary.partials.create.form', [
			'beneficiary' => $beneficiary,
			'readonly' => true,
		])
		<x-slot:footer>
			<a href="{{ route('beneficiary.show', $beneficiary) }}"
				class="btn btn-sm btn-info">
				<i class="fa-solid fa-arrow-up-right-from-square"></i>
				View Beneficiary
			</a>
		</x-slot:footer>
	</x-card>
	<x-card>
		<x-slot:header>Beneficiary's Family Composition</x-slot:header>
		@foreach ($family as $member)
			@include('beneficiary.family.partials.show', [
				'family' => $member,
				'readonly' => true,
			])
			<hr>
		@endforeach
	</x-card>
	<x-card>
		<x-slot:header>Submitted Documents</x-slot:header>
		<div class="alert alert-warning mb-3"
			role="alert">
			<i class="fa-solid fa-info-circle me-2"></i><strong>Note:</strong>
			During the interview, please bring hard copies of the documents you have submitted as soft copies.
		</div>

		@include('application.partials.documents', [
			'readonly' => true,
		])
	</x-card>
	@role('staff')
		{{-- TODO add interview history --}}
		{{-- TODO add assessment info --}}
		{{-- TODO add recommendation info --}}
	@endrole
@endsection
