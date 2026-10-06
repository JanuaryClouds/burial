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
			@if ($recommendation->remarks)
			@endif
		</div>
	</div>
	@php
		$workflowHistory = $recommendation
		    ->workflowHistory()
		    ->with(['toStage', 'remarks', 'fromStage'])
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
					{{-- start::Stage Name --}}
					<span class="text-uppercase fw-bold">
						{{ $history->toStage->name }}
					</span>
					{{-- end::Stage Name --}}

					@if ($history->remarks?->count() > 0)
						{{-- start::Details Button --}}
						<span>
							<button type="button"
								class="btn btn-info btn-sm"
								data-bs-toggle="modal"
								data-bs-target="#showHistoryDetailsModal"
								wire:click="showHistoryDetails('{{ $history->uuid }}')">
								Details
							</button>
						</span>
						{{-- end::Details Button --}}
					@endif
				</div>
			</div>
		@endif
	@endforeach
@endforeach
