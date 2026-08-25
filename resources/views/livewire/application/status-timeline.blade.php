<div class="d-flex flex-column gap-4"
	wire:poll.10s="refresh()">
	@php
		$statusIndicators = [
		    ['pending' => 'current'],
		    ['assessment' => false],
		    ['processed' => false],
		    ['releasing' => false],
		    ['closed' => false],
		];

		for ($i = 0; $i < count($statusIndicators); $i++) {
		    $currentLabel = key($statusIndicators[$i]);
		    if (collect($status)->pluck('label')->contains($currentLabel)) {
		        $statusIndicators[$i][$currentLabel] = 'current';

		        if ($i !== 0) {
		            $previousLabel = key($statusIndicators[$i - 1]);
		            $statusIndicators[$i - 1][$previousLabel] = 'completed';
		        }
		    }
		}

		foreach ($statusIndicators as $key => $value) {
		    if (is_array($value)) {
		        $label = key($value);
		        $statusIndicators[$label] = $value[$label];
		        unset($statusIndicators[$key]);
		    }
		}
	@endphp
	<div class="stepper stepper-pills">
		<div class="stepper-nav flex-center justify-content-around align-items-center">
			@foreach ($statusIndicators as $label => $indicator)
				<div
					class="stepper-item {{ $indicator === 'completed' ? 'completed' : ($indicator === 'current' ? 'current' : '') }}">
					<div class="stepper-wrapper d-flex align-items-center">
						<div class="stepper-icon w-60px h-60px">
							@if ($indicator === 'completed')
								<i class="stepper-check fas fa-check text-success fs-2"></i>
							@else
								<span class="stepper-number">{{ $loop->index + 1 }}</span>
							@endif
						</div>
						<div class="stepper-label">
							<h3
								class="stepper-title {{ $indicator === 'completed' ? 'text-success' : ($indicator === 'current' ? 'text-primary' : 'text-muted') }}">
								Step {{ $loop->index + 1 }}
							</h3>
							<div
								class="stepper-desc {{ $indicator === 'completed' ? 'text-success' : ($indicator === 'current' ? 'text-primary' : 'text-muted') }}">
								{{ str($label)->title() }}
							</div>
						</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</div>
