<div wire:poll.60s>
	@canany(['reject', 'refer'], [$application])
		{{-- start::Select Mode --}}
		<x-form.select wire:model.live='closeMode'
			name="closeMode"
			:options="$closeModes"
			:required="true"
			label="Mode" />
		{{-- end::Select Mode --}}
	@endcanany

	{{-- start::Refer Application --}}
	@can('refer', [$application])
		@if ($closeMode == 'referral')
			<livewire:referral.create :application="$application" />
		@endif
	@endcan
	{{-- end::Refer Application --}}

	{{-- start::Reject Application --}}
	@can('reject', [$application])
		@if ($closeMode == 'rejection')
			<livewire:rejection.create :application="$application" />
		@endif
	@endcan
	{{-- end::Reject Application --}}

	{{-- start::Close Application --}}
	@can('close', [$application])
		<div class="d-flex flex-column gap-4">
			@if ($application->referral)
				<livewire:referral.show :application="$application"
					defer />
			@endif
			@if ($application->rejection)
				<livewire:rejection.show :application="$application"
					defer />
			@endif
			<livewire:closure.create :application="$application"
				defer />
		</div>
	@endcan
	{{-- end::Close Application --}}

	@if ($application->closure)
		<div class="d-flex flex-column gap-4">
			<livewire:closure.show :application="$application"
				defer />
			<div class="d-flex justify-content-end">
				<a href="{{ route('application.show', $application) }}"
					class="btn btn-sm btn-primary">
					<x-icon.font-awesome class="fa-external-link" />
					Go to Application
				</a>
			</div>
		</div>
	@endif
</div>
