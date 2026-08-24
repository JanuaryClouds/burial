<div class="timeline-label"
	wire.poll.10s>
	@foreach ($workflowHistory as $history)
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
				<span class="text-uppercase fw-bold">{{ $history->toStage->name }}</span>

				{{-- Stage:Extra Fields --}}


				{{-- Stage:Remark --}}
				<span class="small text-muted">Sample Remark</span>
			</div>
		</div>
	@endforeach
</div>
