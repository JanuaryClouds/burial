<div class="d-flex flex-column gap-6">
	<h5 class="">Remarks for this {{ Str::headline(class_basename($model)) }}</h5>
	<div class="row h-500px">
		<div class="col-12 col-lg-8 h-100">
			@if ($loading)
				<x-card.loading />
			@else
				<div class="d-flex flex-column gap-6 h-100 overflow-y-scroll"
					wire:poll.60s='refreshRemarks'>
					@if ($remarks && $remarks->count() > 0)
						@foreach ($remarks as $remark)
							<x-callout class="bg-gray-100 border-primary-subtle">
								<x-slot:icon>
									<x-icon.font-awesome class="fa-message text-primary" />
								</x-slot:icon>
								<x-slot:title>
									<div class="d-flex justify-content-between align-content-center">
										<p class="text-primary fw-bold me-4">{{ $remark->user?->fullname() ?? 'N/A' }}</p>
										<p class="text-muted">{{ \Carbon\Carbon::parse($remark->created_at)->format('M d, Y h:i A') }}</p>
									</div>
								</x-slot:title>
								{{ $remark->content }}
							</x-callout>
						@endforeach
					@else
						<div class="d-flex flex-center">
							<p class="text-muted">No Remarks Found</p>
						</div>
					@endif
				</div>
			@endif
		</div>
		<div class="col-12 col-lg-4">
			<div class="d-flex flex-column gap-4">
				<x-callout class="bg-warning-subtle border-warning text-warning">
					<x-slot:icon>
						<x-icon.font-awesome class="fa-info-circle text-warning" />
					</x-slot:icon>
					<p>
						Remarks are not visible to clients. This is for internal use only. Keep all remarks confidential and professional.
					</p>
				</x-callout>
				<livewire:remark.create :model="$model" />
			</div>
		</div>
	</div>
</div>
