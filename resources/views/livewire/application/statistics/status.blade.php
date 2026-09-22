<div wire:poll.30s="getCount">
	<livewire:counter :count="$count"
		:label="ucfirst($status)"
		:iconName="$iconName"
		:iconPathsCount="$iconPathsCount"
		:route="$route" />
</div>
