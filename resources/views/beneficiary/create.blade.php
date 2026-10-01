@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-6">
		{{-- start::Warnings --}}
		@include('beneficiary.create.partials.warnings')
		{{-- end::Warnings --}}

		{{-- start::Beneficiary Create --}}
		<livewire:beneficiary.create defer />
		{{-- end::Beneficiary Create --}}
	</div>
@endsection
