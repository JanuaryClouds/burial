<div wire:poll.30s="refresh">
	<livewire:counter :label="$label"
		:count="$count"
		:icon-name="$iconName"
		:icon-paths-count="$iconPathsCount"
		:route="route('beneficiary.index')" />
</div>
