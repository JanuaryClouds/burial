<x-card>
	<x-slot:header>Status</x-slot:header>
	<div class="d-flex flex-column gap-4">
		<livewire:application.status-timeline :application="$application"
			defer />
		<div class="row">
			@if ($application->rejection || $application->referral || $application->cancellation)
				<div class="col-12 col-lg-6">
					@if ($application->rejection)
						<livewire:rejection.show :application="$application"
							defer />
					@endif
					@if ($application->referral)
						<livewire:referral.show :application="$application"
							defer />
					@endif
					@if ($application->cancellation)
						<livewire:cancellation.show :application="$application"
							defer />
					@endif
				</div>
				<div class="col-12 col-lg-6">
					@if ($application->closure)
						<livewire:closure.show :application="$application"
							defer />
					@else
						<x-callout class="bg-info-subtle text-info border-info">
							<x-slot:icon>
								<x-icon.font-awesome class="fa-info-circle text-info" />
							</x-slot:icon>
							<x-slot:title>
								<p class="text-info fw-bold">Application is still active</p>
							</x-slot:title>
							<p class="text-info">The application has not been closed yet.</p>
							<a href="{{ route('application.close', $application) }}"
								class="btn btn-outline btn-outline-info text-info btn-sm">
								<x-icon.font-awesome class="fa-external-link text-info" />
								Close Application
							</a>
						</x-callout>
					@endif
				</div>
			@endif
		</div>
	</div>
</x-card>
