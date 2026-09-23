<x-card>
	<x-slot:header>Status</x-slot:header>
	<div class="d-flex flex-column gap-4">
		<livewire:application.status-timeline :application="$application"
			defer />
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
</x-card>
