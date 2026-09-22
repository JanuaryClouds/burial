<div wire:poll.30s='getCount'>
	<livewire:counter :count="$count"
		:label="$label"
		:iconName="$iconName"
		:iconPathsCount="$iconPathsCount" />
</div>
