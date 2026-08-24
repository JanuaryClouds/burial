@php
	$statusIndicators = ['pending', 'assessment', 'processed', 'releasing', 'closed'];
@endphp
<div class="d-flex flex-column gap-4">
	<div class="stepper stepper-pills">
		<div class="stepper-nav flex-center justify-content-around align-items-center">
			@foreach ($statusIndicators as $indicator)
				@php
					$isCompleted = collect($status)->pluck('label')->contains($indicator);
					$isCurrent =
					    !$isCompleted &&
					    collect($status)
					        ->pluck('label')
					        ->contains($statusIndicators[$loop->index - 1]) &&
					    !collect($status)
					        ->pluck('label')
					        ->contains($statusIndicators[$loop->index + 1]);
				@endphp
				<div class="stepper-item {{ $isCompleted ? 'completed' : ($isCurrent ? 'current' : '') }}">
					<div class="stepper-wrapper d-flex align-items-center">
						<div class="stepper-icon w-60px h-60px">
							@if ($isCompleted)
								<i class="stepper-check fas fa-check text-success fs-2"></i>
							@else
								<span class="stepper-number">{{ $loop->index + 1 }}</span>
							@endif
						</div>
						<div class="stepper-label">
							<h3 class="stepper-title {{ $isCompleted ? 'text-success' : ($isCurrent ? 'text-primary' : 'text-muted') }}">
								Step {{ $loop->index + 1 }}
							</h3>
							<div class="stepper-desc {{ $isCompleted ? 'text-success' : ($isCurrent ? 'text-primary' : 'text-muted') }}">
								{{ str($indicator)->title() }}
							</div>
						</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</div>
