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
			<div class="col-12 col-lg-6"
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
			<div class="col-12 col-lg-6"
				wire:poll.120s>
				{{-- Rejection --}}
				@include('application.show.partials.rejection')
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
				<div class="col-12 col-xl-5 d-flex flex-column gap-6"
					id="workflow-history-create-form"
					wire:poll.60s>
					{{-- Create Workflow History --}}
					@include('application.show.partials.workflow.history.create')
				</div>
			</div>
		@endif
	@endrole
	<div id="documents"
		wire:ignore>
		<x-card>
			{{-- Documents --}}
			<x-slot:header>Documents</x-slot:header>
			@include('application.show.partials.documents')
		</x-card>
	</div>
</div>
