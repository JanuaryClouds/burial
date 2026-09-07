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
							<x-icon.font-awesome class="fa-lock fs-4" />
							@if (!$application->finishedInterview())
								<p class="fs-4">Interview the client before making an assessment</p>
								<a class="btn btn-sm btn-light"
									href="{{ route('client.show', $client) }}"
									role="button">
									<x-icon.font-awesome class="fa-arrow-up-right-from-square" />
									View Client
								</a>
							@else
								<p class="fs-4">You do not have permission to assess the client</p>
							@endif
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
							<x-icon.font-awesome class="fa-lock fs-4" />
							@if (!$application->assessment)
								<p class="fs-4">Write an assessment first before making a recommendation</p>
							@else
								<p class="fs-4">You do not have permission to create a recommendation</p>
							@endif
						</x-card.unauthorized>
					@endcan
				</div>
			@endif
		</div>
		@if ($application->recommendations->count() > 0)
			<div class="row">
				<div class="col-12 col-xl-7"
					id="workflow-history">
					<x-card>
						<x-slot:header>Process Timeline</x-slot:header>
						<livewire:application.timeline :application="$application" />
					</x-card>
				</div>
				<div class="col-12 col-xl-5"
					id="workflow-history-create-form">
					@can('create', [\App\Models\WorkflowHistory::class, $application])
						<livewire:workflow.history.create :application="$application" />
					@else
						<x-card.unauthorized>
							<x-icon.font-awesome class="fa-lock fs-4" />
							<p class="fs-5 fw-semibold">
								@if ($application->referral)
									Application has been referred
								@endif
								@if ($application->cancellation)
									Application has been cancelled
								@endif
							</p>
						</x-card.unauthorized>
					@endcan
				</div>
			</div>
		@endif
	@endrole
	<div id="documents">
		<x-card>
			<x-slot:header>Documents</x-slot:header>
			@include('application.partials.documents')
		</x-card>
	</div>
@endsection
