@extends('layouts.app')
@section('content')
    @can('view-walk-in-claim')
        <div class="alert alert-info">Already applied at CSWDO in person? <a href="{{ route('walk-in.claim') }}">Link your walk-in records</a> using your private claim code.</div>
    @endcan
	<div class="d-flex flex-column gap-6">
		{{-- start::Documents --}}
		<x-card>
			<x-slot:header>Required Documents</x-slot:header>
			@include('client.create.partials.documents')
		</x-card>
		{{-- end::Documents --}}

		{{-- start::Warnings --}}
		<x-card>
			<x-slot:header>Notice</x-slot:header>
			@include('client.create.partials.warnings')
		</x-card>
		{{-- end::Warnings --}}


		{{-- start::Form --}}
		<livewire:client.create defer />
		{{-- end::Form --}}
	</div>
@endsection
