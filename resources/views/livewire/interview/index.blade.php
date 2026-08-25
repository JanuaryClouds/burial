<div wire:poll.10s="refresh()">
	@if ($interviews->count() > 0)
		<div class="timeline-label">
			@foreach ($interviews as $interview)
				<div class="timeline-item">
					<div class="timeline-label">
						<span class="text-uppercase fw-bold">
							{{ \Carbon\Carbon::parse($interview->schedule)->format('H:i') }}
						</span>
						<br>
						<span class="text-uppercase small text-muted fw-semibold">
							{{ \Carbon\Carbon::parse($interview->schedule)->format('M d') }}
						</span>
					</div>
					<div class="timeline-badge">
						<i class="fa-solid fa-genderless text-primary fs-1"></i>
					</div>
					<div class="timeline-content ms-3 d-flex flex-column">
						<span class="text-uppercase fw-bold">{{ $interview->status }}</span>
						@if ($interview->status !== 'done')
							<x-button wire:click="markAsDone({{ $interview }})"
								class="btn-sm btn-success w-fit mt-1">
								Mark As Done
							</x-button>
						@endif
					</div>
				</div>
			@endforeach
		</div>
	@else
		<div class="d-flex flex-center">
			<span>No interview history found.</span>
		</div>
	@endif
</div>
