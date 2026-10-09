<div wire:poll.10s='refresh()'>
	@if ($recommendations->count() > 0)
		<div class="timeline-label">
			{{-- start::Submission Date --}}
			@include('workflow.history.index.partials.submission-date')
			{{-- end::Submission Date --}}

			{{-- start::Per Recommendation Timeline --}}
			@include('workflow.history.index.partials.recommendation-timeline')
			{{-- end::Per Recommendation Timeline --}}

			{{-- start::Application Close --}}
			@include('workflow.history.index.partials.application-close')
			{{-- end::Application Close --}}

			{{-- start::Show Workflow History Modal --}}
			@include('workflow.history.index.partials.show.modal')
			{{-- end::Show Workflow History Modal --}}

			{{-- start::Show Remarks Modal --}}
			@include('workflow.history.index.partials.remarks.modal')
			{{-- end::Show Remarks Modal --}}
		</div>
	@else
		<div class="d-flex flex-center">
			<span>No process history found.</span>
		</div>
	@endif
</div>
