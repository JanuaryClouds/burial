<section class="card dashboard-activity" aria-labelledby="recent-applications-heading">
	<div class="card-header flex-shrink-0">
		<h2 id="recent-applications-heading" class="card-title fs-4">Recently submitted applications</h2>
	</div>
	<div class="dashboard-activity-list" tabindex="0" role="region"
		aria-label="Recently submitted applications" data-dashboard-activity-scroll>
		<ul class="list-unstyled mb-0">
			@forelse ($recentApplications as $application)
				@php($status = $application->currentStatus())
				<li class="dashboard-activity-item border-bottom" wire:key="recent-application-{{ $application->uuid }}">
					<div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
						<a class="fw-semibold" href="{{ route('application.show', $application) }}">
							{{ $application->tracking_no }}
						</a>
						<span class="badge badge-light-{{ $status['badgeColor'] }}">{{ ucfirst($status['label']) }}</span>
					</div>
					<time class="d-block text-muted fs-7 mt-2" datetime="{{ $application->created_at->toIso8601String() }}">
						{{ $application->created_at->format('M d, Y · g:i A') }}
					</time>
				</li>
			@empty
				<li class="p-6 text-muted">No applications submitted yet</li>
			@endforelse
		</ul>
	</div>
</section>
