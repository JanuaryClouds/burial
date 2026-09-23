<div wire:poll.10s='refresh()'>
	@if ($recommendations->count() > 0)
		<div class="timeline-label">
			<div class="timeline-item">
				<div class="timeline-label">
					<span class="text-uppercase fw-bold">
						{{ \Carbon\Carbon::parse($application->created_at)->format('H:i') }}
					</span>
					<br>
					<span class="text-uppercase small text-muted fw-semibold">
						{{ \Carbon\Carbon::parse($application->created_at)->format('M d') }}
					</span>
				</div>
				<div class="timeline-badge">
					<i class="fa-solid fa-genderless text-primary fs-1"></i>
				</div>
				<div class="timeline-content ms-3 d-flex flex-column">
					<span class="text-uppercase fw-bold">
						Submission
					</span>
					<span class="small text-muted">Application has been submitted</span>
				</div>
			</div>
			@foreach ($recommendations as $recommendation)
				<div class="timeline-item">
					<div class="timeline-label">
						<span class="text-uppercase fw-bold">
							{{ \Carbon\Carbon::parse($recommendation->created_at)->format('H:i') }}
						</span>
						<br>
						<span class="text-uppercase small text-muted fw-semibold">
							{{ \Carbon\Carbon::parse($recommendation->created_at)->format('M d') }}
						</span>
					</div>
					<div class="timeline-badge">
						<i class="fa-solid fa-genderless text-primary fs-1"></i>
					</div>
					<div class="timeline-content ms-3 d-flex flex-column">
						<span class="text-uppercase fw-bold">
							Recommendation: {{ $recommendation->funeralAssistanceType?->name ?? 'N/A' }}
						</span>
						<span class="small text-muted">Sample Remark</span>
					</div>
				</div>
				@php
					$workflowHistory = $recommendation
					    ->workflowHistory()
					    ->with(['toStage', 'fromStage'])
					    ->orderBy('date_in')
					    ->get();
				@endphp
				@foreach ($workflowHistory as $history)
					@if ($history->toStage)
						<div class="timeline-item">
							<div class="timeline-label">
								<span class="text-uppercase fw-bold">
									{{ \Carbon\Carbon::parse($history->date_in)->format('H:i') }}
								</span>
								<br>
								<span class="text-uppercase small text-muted fw-semibold">
									{{ \Carbon\Carbon::parse($history->date_in)->format('M d') }}
								</span>
							</div>
							<div class="timeline-badge">
								<i class="fa-solid fa-genderless text-primary fs-1"></i>
							</div>
							<div class="timeline-content ms-3 d-flex flex-column">
								{{-- Stage:Name --}}
								<span class="text-uppercase fw-bold">
									{{ $history->toStage->name }}
								</span>

								{{-- Stage:Extra Fields --}}


								{{-- Stage:Remark --}}
								<span class="small text-muted">Sample Remark</span>
							</div>
						</div>
					@endif
				@endforeach
			@endforeach
			@if ($application->referral || $application->cancellation || $application->rejection)
				<div class="timeline-item">
					<div class="timeline-label">
						<span class="text-uppercase fw-bold">
							@if ($application->referral)
								{{ \Carbon\Carbon::parse($application->referral->created_at)->format('H:i') }}
							@endif
							@if ($application->cancellation)
								{{ \Carbon\Carbon::parse($application->cancellation->created_at)->format('H:i') }}
							@endif
							@if ($application->rejection)
								{{ \Carbon\Carbon::parse($application->rejection->created_at)->format('H:i') }}
							@endif
						</span>
						<br>
						<span class="text-uppercase small text-muted fw-semibold">
							@if ($application->referral)
								{{ \Carbon\Carbon::parse($application->referral->created_at)->format('M d') }}
							@endif
							@if ($application->cancellation)
								{{ \Carbon\Carbon::parse($application->cancellation->created_at)->format('M d') }}
							@endif
							@if ($application->rejection)
								{{ \Carbon\Carbon::parse($application->rejection->created_at)->format('M d') }}
							@endif
						</span>
					</div>
					<div class="timeline-badge">
						<i class="fa-solid fa-genderless text-primary fs-1"></i>
					</div>
					<div class="timeline-content ms-3 d-flex flex-column">
						{{-- Stage:Name --}}
						<span class="text-uppercase fw-bold">
							@if ($application->referral)
								Referred
							@endif
							@if ($application->cancellation)
								Cancelled
							@endif
							@if ($application->rejection)
								Rejected
							@endif
						</span>

						{{-- Stage:Extra Fields --}}
						<span>
							@if ($application->referral)
								Referred to {{ $application->referral->referral_to }}
							@endif
							@if ($application->cancellation)
								Reason for Cancellation: {{ $application->cancellation->reason }}
							@endif
							@if ($application->rejection)
								Reason for Rejection: {{ $application->rejection->reason }}
							@endif
						</span>

						{{-- Stage:Remark --}}
						<span class="small text-muted">Sample Remark</span>
					</div>
				</div>
			@endif
		</div>
	@else
		<div class="d-flex flex-center">
			<span>No process history found.</span>
		</div>
	@endif
</div>
