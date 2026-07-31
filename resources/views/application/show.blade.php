@extends('layouts.app')
@section('content')
	@role('staff')
		@include('application.partials.menu')
	@endrole
	{{-- TODO add timeline --}}
	<x-card>
		<x-slot:header>Client</x-slot:header>
		@include('client.partials.show', [
			'client' => $application->client,
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
		@include('beneficiary.partials.show', [
			'beneficiary' => $beneficiary,
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
			])
			<hr>
		@endforeach
	</x-card>
	{{-- TODO add submitted documents --}}
	@role('staff')
		{{-- TODO add interview history --}}
		{{-- TODO add assessment info --}}
		{{-- TODO add recommendation info --}}
	@endrole
@endsection
