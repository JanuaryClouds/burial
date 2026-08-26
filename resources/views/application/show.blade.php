@extends('layouts.app')
@section('content')
	<div class="row">
		<div class="col-12 col-lg-8 mb-6 mb-lg-0">
			<livewire:application.summary :application="$application" />
		</div>
		<div class="col-12 col-lg-4">
			@include('application.partials.navigation')
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<x-card id="status">
				<x-slot:header>Status</x-slot:header>
				<livewire:application.status-timeline :application="$application" />
			</x-card>
		</div>
	</div>
	@role('staff')
		<div class="row">
			@if (!$application->assessment)
				<div class="col-12 col-lg-6 mb-6 mb-lg-0"
					id="assessment">
					@can('create', [\App\Models\Assessment::class, $application])
						<livewire:assessment.create :application="$application" />
					@else
						<x-card.unauthorized>
							<x-slot:header>Assessment</x-slot:header>
						</x-card.unauthorized>
					@endcan
				</div>
			@endif
			@if ($application->recommendations->count() == 0)
				<div class="col-12 col-lg-6"
					id="recommendation">
					@can('create', [\App\Models\Recommendation::class, $application])
						<livewire:recommendation.create :application="$application" />
					@else
						<x-card.unauthorized>
							<x-slot:header>Recommendation</x-slot:header>
						</x-card.unauthorized>
					@endcan
				</div>
			@endif
		</div>
		<div class="row">
			<div class="col-12 col-lg-8 mb-6 mb-lg-0"
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
	<div id="documents">
		<x-card>
			<x-slot:header>Documents</x-slot:header>
			@include('application.partials.documents')
		</x-card>
	</div>
@endsection
