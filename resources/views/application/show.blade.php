@extends('layouts.app')
@section('content')
	<div class="row">
		<div class="col-12 col-lg-8">
			<livewire:application.summary :application="$application" />
		</div>
		<div class="col-12 col-lg-4">
			@include('application.partials.navigation')
		</div>
	</div>
	<x-card id="status">
		<x-slot:header>Status</x-slot:header>
		<livewire:application.status-timeline :application="$application" />
	</x-card>
	@role('staff')
		<div class="row">
			<div class="col-12 col-lg-8"
				id="workflow-history">
				<x-card>
					<x-slot:header>Process Timeline</x-slot:header>
					<livewire:application.timeline :application="$application" />
				</x-card>
			</div>
			<div class="col-12 col-lg-4">
				{{-- Livewire:Workflow-History/Create --}}
			</div>
		</div>
	@endrole
	<div class="row">
		{{-- Partial:Documents --}}
		@role('staff')
			{{-- Partial:Interview-History --}}
			{{-- Partial:Assessment --}}
			{{-- Partial:Recommendation --}}
		@endrole
	</div>
@endsection
