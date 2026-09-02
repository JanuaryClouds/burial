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
							Recommendation: {{ $recommendation->funeralAssistanceType->name }}
						</span>
						<span class="small text-muted">Sample Remark</span>
					</div>
				</div>
				@php
					$workflowHistory = $recommendation->workflowHistory()->orderBy('date_in')->get();
				@endphp
				@foreach ($workflowHistory as $history)
					@if ($history->fromStage != null)
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
									{{ $history->fromStage->name }}
								</span>

								{{-- Stage:Extra Fields --}}


								{{-- Stage:Remark --}}
								<span class="small text-muted">Sample Remark</span>
							</div>
						</div>
					@endif
				@endforeach
			@endforeach
			@if ($application->referral)
				<div class="timeline-item">
					<div class="timeline-label">
						<span class="text-uppercase fw-bold">
							{{ \Carbon\Carbon::parse($application->referral->created_at)->format('H:i') }}
						</span>
						<br>
						<span class="text-uppercase small text-muted fw-semibold">
							{{ \Carbon\Carbon::parse($application->referral->created_at)->format('M d') }}
						</span>
					</div>
					<div class="timeline-badge">
						<i class="fa-solid fa-genderless text-primary fs-1"></i>
					</div>
					<div class="timeline-content ms-3 d-flex flex-column">
						{{-- Stage:Name --}}
						<span class="text-uppercase fw-bold">
							Referral
						</span>

						{{-- Stage:Extra Fields --}}
						<span>Referred to {{ $application->referral->referral_to }}</span>

						{{-- Stage:Remark --}}
						<span class="small text-muted">Sample Remark</span>
					</div>
				</div>
			@endif
			@if ($application->cancellation)
				<div class="timeline-item">
					<div class="timeline-label">
						<span class="text-uppercase fw-bold">
							{{ \Carbon\Carbon::parse($application->cancellation->created_at)->format('H:i') }}
						</span>
						<br>
						<span class="text-uppercase small text-muted fw-semibold">
							{{ \Carbon\Carbon::parse($application->cancellation->created_at)->format('M d') }}
						</span>
					</div>
					<div class="timeline-badge">
						<i class="fa-solid fa-genderless text-primary fs-1"></i>
					</div>
					<div class="timeline-content ms-3 d-flex flex-column">
						{{-- Stage:Name --}}
						<span class="text-uppercase fw-bold">
							Cancelled
						</span>

						{{-- Stage:Extra Fields --}}


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
