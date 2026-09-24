<div>
	@if (Auth::user()->can('refer', [$application]) || Auth::user()->can('reject', [$application]))
		<x-form.select wire:model.live='stopMode'
			name="stopMode"
			:options="$stopModes"
			:required="true"
			label="Mode" />
		@can('refer', [$application])
			@if ($stopMode == 'referral')
				<livewire:referral.create :application="$application" />
			@endif
		@endcan
		@can('reject', [$application])
			@if ($stopMode == 'rejection')
				<livewire:rejection.create :application="$application" />
			@endif
		@endcan
	@endif
</div>
