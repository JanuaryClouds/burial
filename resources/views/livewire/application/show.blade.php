<x-slot:page_title>{{ $application->tracking_no }} | Application</x-slot:page_title>
<x-slot:page_subtitle>Funeral Assistance System | CSWDO Taguig</x-slot:page_title>
<div class="d-flex flex-column gap-6">
	<div class="row">
		<div class="col-12 col-lg-8"
			id="application-summary"
			wire:poll.30s>
			{{-- Application Summary --}}
			@include('application.show.partials.summary')
		</div>
		<div class="col-12 col-lg-4">
			{{-- Page Navigation --}}
			@include('application.partials.navigation')
		</div>
	</div>
	<div class="row">
		<div class="col-12"
			id="status"
			wire:poll.30s>
			{{-- Status Timeline --}}
			@include('application.show.partials.status-timeline')
		</div>
	</div>
	@role('staff')
		<div class="row">
			<div class="col-12 col-lg-6 mb-6 mb-lg-0"
				id="assessment"
				wire:poll.300s>
				{{-- Assessment --}}
				@include('application.show.partials.assessment')
			</div>
			<div class="col-12 col-lg-6"
				id="recommendation"
				wire:poll.120s>
				{{-- Recommendation --}}
				@include('application.show.partials.recommendation')
			</div>
		</div>
		@if ($application->recommendations->count() > 0)
			<div class="row">
				<div class="col-12 col-xl-7"
					id="workflow-history"
					wire:poll.60s>
					{{-- Workflow History --}}
					@include('application.show.partials.workflow.history.index')
				</div>
				<div class="col-12 col-xl-5"
					id="workflow-history-create-form"
					wire:poll.60s>
					{{-- Create Workflow History --}}
					@include('application.show.partials.workflow.history.create')
				</div>
			</div>
		@endif
	@endrole
	<div id="documents">
		<x-card>
			{{-- Documents --}}
			<x-slot:header>Documents</x-slot:header>
			@include('application.partials.documents')
		</x-card>
	</div>
</div>
