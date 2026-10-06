<div class="d-flex flex-column gap-6">
	@if ($model)
		@foreach ($remarks as $remark)
			<div class="d-flex flex-column gap-4">
				<div>
					<span class="fw-bold text-uppercase">{{ $remark->user?->name ?? 'N/A' }}</span>
				</div>
				<span>{{ $remark->remarks }}</span>
				<span>{{ \Carbon\Carbon::parse($remark->created_at)->format('M d, Y h:i A') }}</span>
			</div>
		@endforeach
	@else
		<div class="d-flex flex-center">
			<p class="text-muted">No Remarks Found</p>
		</div>
	@endif
</div>
