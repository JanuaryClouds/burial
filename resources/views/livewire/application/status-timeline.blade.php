<div class="d-flex flex-column">
	<div class="stepper stepper-pills">
		<div class="stepper-nav flex-wrap flex-lg-nowrap d-flex justify-content-around align-items-center">
			@foreach ($statusIndicators as $label => $indicator)
				<div
					class="stepper-item w-100 w-lg-auto {{ $indicator === 'completed' ? 'completed' : ($indicator === 'current' ? 'current' : '') }}">
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
	@if ($application->cancellation || $application->referral)
		<div class="separator separator-dashed my-4"></div>
		<div class="stepper stepper-pills">
			<div class="stepper-nav flex-wrap flex-lg-nowrap d-flex justify-content-around align-items-center">
				<div class="stepper-item w-100 w-lg-auto completed">
					<div class="stepper-wrapper d-flex align-items-center">
						<div class="stepper-icon w-60px h-60px">
							<i class="stepper-check fas fa-check text-success fs-2"></i>
						</div>
						<div class="stepper-label">
							@if ($application->cancellation)
								<h3 class="stepper-title completed text-success">
									Cancelled
								</h3>
								<div class="stepper-desc completed text-success">
									{{ $application->cancellation->reason }}
								</div>
							@endif
							@if ($application->referral)
								<h3 class="stepper-title completed text-success">
									Referred
								</h3>
								<div class="stepper-desc completed text-success">
									Referred to {{ $application->referral->referral_to }}
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	@endif
</div>
