<div wire:poll.30s="getCount">
	<livewire:counter :label="$label"
		:count="$count"
		:icon-name="$iconName"
		:icon-paths-count="$iconPathsCount"
		:route="route('beneficiary.index')" />
</div>
