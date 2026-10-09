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
