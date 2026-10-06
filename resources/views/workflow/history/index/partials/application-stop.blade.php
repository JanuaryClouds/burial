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
					Referred to {{ $application->referral->referred_to }}
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
