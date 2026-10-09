<div class="d-flex flex-column gap-6">
	{{-- start::Workflow History Details --}}
	<div class="d-flex flex-column gap-6">
		@if ($selectedHistory)
			<h4>{{ $selectedHistory->toStage?->name }}</h4>
			<p class="fs-4">{{ $selectedHistory->toStage?->description }}</p>
			<div class="d-flex justify-content-between align-content-center">
				<p class="text-muted fw-semibold">
					<x-icon.font-awesome class="fa-user me-2" />
					{{ $selectedHistory->processedBy?->fullname() }}
				</p>
				<p class="text-muted">
					<x-icon.font-awesome class="fa-calendar me-2" />
					{{ \Carbon\Carbon::parse($selectedHistory->date_in)->format('d M y h:i A') }}
				</p>
			</div>
		@endif
	</div>
	{{-- end::Workflow History Details --}}

	<div class="row">
		{{-- start::Workflow History Extra Fields --}}
		<div class="col-12 col-lg-6">

		</div>
		{{-- end::Workflow History Extra Fields --}}
	</div>
</div>
