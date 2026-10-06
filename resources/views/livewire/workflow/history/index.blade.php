<div wire:poll.10s='refresh()'>
	@if ($recommendations->count() > 0)
		<div class="timeline-label">
			{{-- start::Submission Date --}}
			@include('workflow.history.index.partials.submission-date')
			{{-- end::Submission Date --}}

			{{-- start::Per Recommendation Timeline --}}
			@include('workflow.history.index.partials.recommendation-timeline')
			{{-- end::Per Recommendation Timeline --}}

			{{-- start::Application Stop --}}
			@include('workflow.history.index.partials.application-stop')
			{{-- end::Application Stop --}}

			<livewire:workflow.history.show />
		</div>
	@else
		<div class="d-flex flex-center">
			<span>No process history found.</span>
		</div>
	@endif
</div>
